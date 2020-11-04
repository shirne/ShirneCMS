<?php

namespace app\common\model;


use app\common\core\BaseOrderModel;
use app\common\service\MessageService;
use think\facade\Db;
use think\Exception;
use think\facade\Log;

define('ORDER_STATUS_REFUND',-2);
define('ORDER_STATUS_CANCEL',-1);
define('ORDER_STATUS_UNPAIED',0);
define('ORDER_STATUS_PAIED',1);
define('ORDER_STATUS_SHIPED',2);
define('ORDER_STATUS_RECEIVED',3);
define('ORDER_STATUS_FINISH',4);

/**
 * Class OrderModel
 * @package app\common\model
 */
class OrderModel extends BaseOrderModel
{
    protected $name = 'order';
    protected $pk='order_id';

    public function onAfterWrite($model)
    {
        $where=$model->getWhere();if(empty($where))return;
        $orders=$model->where($where)->select();
        if(!empty($orders)) {
            foreach ($orders as $order) {
                if ($order['status'] > 0 && $order['isaudit'] == 1) {
                    self::setLevel($order);
                    $rebated=self::doRebate($order);
                    if($rebated){
                        Db::name('Order')->where('order_id',$order['order_id'])
                            ->update(['rebated'=>1,'rebate_time'=>time()]);
                    }
                }
            }
        }
    }
    
    public static function getCounts($member_id=0){
        $model=Db::name('Order')->where('delete_time',0);
        if($member_id>0){
            $model->where('member_id',$member_id);
        }
        $countlist=$model->group('status')->field('status,count(order_id) as order_count')->select();
        $counts=[0,0,0,0,0,0,0];
        foreach ($countlist as $row){
            $counts[$row['status']]=$row['order_count'];
        }
        return $counts;
    }
    
    public function audit(){
        if($this->isExists()){
            $updated=Db::name('Order')->where('order_id',$this['order_id'])
                ->update(['isaudit'=>1]);
            if($updated && $this['status']>0){
                $this->afterAudit($this->getOrigin());
            }
        }else{
            throw new Exception('订单不存在');
        }
    }
    
    protected function afterAudit($item){
        if(!$item['rebated']) {
            self::setLevel($item);
            self::doRebate($item);
        }
    }
    
    protected function triggerStatus($item, $status, $newData=[])
    {
        parent::triggerStatus($item, $status, $newData);
        if($status < -2){
        
        }elseif($status < 0){
            $products=Db::name('orderProduct')->where('order_id',$item['order_id'])->select();
            foreach ($products as $product) {
                Db::name('ProductSku')->where('sku_id', $product['sku_id'])
                    ->dec('storage', -$product['count'])
                    ->inc('sale', $product['count'])
                    ->update();
                Db::name('Product')->where('id', $product['product_id'])
                    ->dec('storage', -$product['count'])
                    ->inc('sale', $product['count'])
                    ->update();
            }
            Db::name('Order')->where('order_id',$item['order_id'])
                ->update(['cancel_time'=>time()]);

            //只传id过去，需要取新数据
            self::sendOrderMessage($item['order_id'],'order_cancel',$products);
    
            AwardLogModel::cancel($item['order_id']);
        }else{
            if($status < $item['status'])return;
            if($item['isaudit'] == 1 || !empty($newData['isaudit'])){
                $this->afterAudit($item);
            }
            if($item['status'] < $status){
                switch ($status){
                    case ORDER_STATUS_PAIED:
                        $this->afterPay($item);
                        break;
                    case ORDER_STATUS_SHIPED:
                        $this->afterDeliver($item);
                        break;
                    case ORDER_STATUS_RECEIVED:
                        $this->afterReceive($item);
                        break;
                    case ORDER_STATUS_FINISH:
                        $this->afterComplete($item);
                        break;
                    default:
                }
            }
        }
    }
    protected function afterPay($item=null){
        if(empty($item) && $this->isExists()){
            $item = $this->getOrigin();
        }
        self::sendOrderMessage($item['order_id'],'order_payed');
    }
    protected function afterDeliver($item=null){
        if(empty($item) && $this->isExists()){
            $item = $this->getOrigin();
        }
        self::sendOrderMessage($item['order_id'],'order_deliver');
    }
    protected function afterReceive($item=null){
        if(empty($item) && $this->isExists()){
            $item = $this->getOrigin();
        }
        self::sendOrderMessage($item['order_id'],'order_receive');
    }
    protected function afterComplete($item=null){
        if(empty($item) && $this->isExists()){
            $item = $this->getOrigin();
        }
        self::sendOrderMessage($item['order_id'],'order_complete');
        AwardLogModel::giveout($item['order_id']);
    }

    /**
     * @param $member
     * @param $products
     * @param $address
     * @param $extdata
     * @param $balance_pay
     * @param $ordertype
     * @return mixed
     */

    public function makeOrder($member, $products, $address, $extdata, $balance_pay=1, $ordertype=1){
        if(empty($member) || empty($member['id'])){
            $this->setError('指定的下单用户资料错误');
            return false;
        }
        //Log::record('create order:'.var_export(func_get_args(),true));
        
        //折扣
        $levels=getMemberLevels();
        $level=$levels[$member['level_id']];
        $discount=1;
        if(!empty($level) && $level['discount']<100){
            $discount = $level['discount']*.01;
        }

        //status 0-待付款 1-已付款
        $status=0;
        //总价 单位分
        $total_price=0;
        //总成本价
        $total_cost_price=0;
        $commission_type= getSetting('commission_type');
        //分佣本金 单位分
        $commission_amount=0;
        $comm_special = [];
        $levelids=[];
        $ordertype=0;
    
        //优惠价格
        $discount_amount=0;
    
        //运费模板
        $postage_area_ids = array_column($products,'postage_area_id');
        if(!empty($postage_area_ids))$postage_area_ids = array_unique(array_filter($postage_area_ids));
        if(!empty($postage_area_ids)){
            $postageareas=PostageModel::getAreaList($postage_area_ids);
        }
        foreach ($products as $k=>$product){
            if($product['storage']<$product['count']){
                $this->setError('商品['.$product['product_title'].']库存不足');
                return false;
            }
            if($product['count']<1){
                $this->setError('商品['.$product['product_title'].']数量错误');
                return false;
            }
    
            if(!empty($product['levels'])){
                if (!in_array($member['level_id'], $product['levels'])) {
                    $this->setError('您当前会员组不允许购买商品[' . $product['product_title'] . ']');
                    return false;
                }
            }
            
            $release_price=$product['product_price'];
            if($level['diy_price']==1 && isset($product['ext_price'][$level['level_id']])){
                $release_price = round($product['ext_price'][$level['level_id']],2);
            }else {
                if ($product['is_discount']) {
                    $release_price = round($release_price * $discount,2);
                }
            }
            $products[$k]['release_price']=$release_price;
            $price = round($release_price * 100 * $product['count']);
            
            $total_price += $price;

            $cost_price = intval($product['cost_price'] * 100) * $product['count'];

            $total_cost_price += $cost_price;
            
            //运费
            if($product['postage_id']>0 && !empty($postageareas)){
                $parea_id=$product['postage_area_id']?:0;
                if(!isset($postageareas[$parea_id])){
                    $this->setError('参数错误');
                    return false;
                }
                
                if(!isset($postageareas[$parea_id]['total'])){
                    $postageareas[$parea_id]['total']=0;
                    $postageareas[$parea_id]['amount']=0;
                }
                if($postageareas[$parea_id]['calc_type']==2){
                    $postageareas[$parea_id]['total']+=static::calc_size($product['size']);
                }elseif($postageareas[$parea_id]['calc_type']==1){
                    $postageareas[$parea_id]['total']+=$product['count'];
                }else{
                    $postageareas[$parea_id]['total']+=$product['weight']*$product['count'];
                }
                $postageareas[$parea_id]['amount']+=$price;
            }

            if($product['is_commission'] == 1 ){
                $orig_price = intval($product['product_price'] * 100) * $product['count'];
                
                if($commission_type==3){
                    $commission_amount += $orig_price;
                }elseif($commission_type==2){
                    $commission_amount += $price;
                }elseif($commission_type==1){
                    if ($orig_price > $cost_price) {
                        $commission_amount += $orig_price - $cost_price;
                    }
                }else {
                    if ($price > $cost_price) {
                        $commission_amount += $price - $cost_price;
                    }
                }
            }elseif($product['is_commission'] == 2){
                
                if($price>$cost_price) {
                    $comm_special[]=[
                        'type'=>2,
                        'amount'=> ($price - $cost_price)*.01,
                        'percent'=>force_json_decode($product['commission_percent'])
                    ];
                }
            }elseif($product['is_commission'] == 3){
                $comm_special[]=[
                    'type'=>3,
                    'count'=>$product['count'],
                    'amounts'=>force_json_decode($product['commission_percent'])
                ];
            }elseif($product['is_commission'] == 4){
                $comm_special[]=[
                    'type'=>4,
                    'count'=>$product['count'],
                    'level_amounts'=>force_json_decode($product['commission_percent'])
                ];
            }
    
            $producttype=intval($product['type']);
            $ordertype = $ordertype | $producttype;
            if(($producttype & PRO_TYPE_BIND) == PRO_TYPE_BIND && !empty($product['level_id'])){
                $levelids[]=$product['level_id'];
            }
        }
        
        //邮费计算
        $postage_fee=0;
        if(!empty($postageareas)) {
            foreach ($postageareas as $aid => $area) {
                
                if ($area['free_limit'] <= 0 || $area['amount'] < $area['free_limit']) {
                    $curfee=$area['first_fee'];
                    if($area['total']>$area['first'] && $area['extend']>0 && $area['extend_fee']>0){
                        $atotal = $area['total']-$area['first'];
                        while($atotal>0){
                            $curfee += $area['extend_fee'];
                            $atotal-=$area['extend'];
                            if($area['ceiling']>0 && $curfee>$area['ceiling']){
                                $curfee=$area['ceiling'];
                                break;
                            }
                        }
                    }
                    $postage_fee+=$curfee;
                }
            }
            $postage_fee = round($postage_fee,2);
        }
    
        $level_id = 0;
        $levelids = array_unique($levelids);
        if(count($levelids)>0) {
            foreach ($levels as $lid=>$level){
                if(in_array($lid,$levelids)){
                    $level_id = $lid;
                }
            }
        }
        
        //比较客户端传来的价格
        if(is_array($extdata) ){
            if(isset($extdata['total_price'])) {
                if ($total_price != round($extdata['total_price']*100)) {
                    $this->setError('下单商品价格已变动');
                    return false;
                }
            }
            
            //邮费价格误差控制在0.5以内
            if( isset($extdata['total_postage'])) {
                if (abs($postage_fee - $extdata['total_postage'])>.5) {
                    $this->setError('邮费价格已变动');
                    return false;
                }else{
                    $postage_fee = round($extdata['total_postage'],2);
                }
            }
        }
        
        $this->startTrans();
        
        if($balance_pay) {
            $debit = money_log($member['id'], -$total_price, "下单支付", 'consume',0,is_string($balance_pay)?$balance_pay:'money');
            if ($debit) $status = ORDER_STATUS_PAIED;
            else{
                $this->rollback();
                $this->setError("余额不足");
                return false;
            }
        }
        $time=time();
        $orderno=$this->create_no();
        Log::record('order no:'.$orderno);
        $orderdata=array(
            'order_no'=>$orderno,
            'member_id'=>$member['id'],
            'level_id'=>$level_id,
            'payamount'=>$total_price*.01 + $postage_fee,
            'postage'=>$postage_fee,
            'product_amount'=>$total_price*.01,
            'discount_amount'=>$discount_amount*.01,
            'cost_amount'=>$total_cost_price*.01,
            'commission_amount'=>$commission_amount*.01,
            'commission_special'=>json_encode($comm_special),
            'status'=> ORDER_STATUS_UNPAIED,
            'isaudit'=>getSetting('autoaudit')==1?1:0,
            //'remark'=>$remark,
            'address_id'=>$address['address_id'],
            'recive_name'=>$address['recive_name'],
            'mobile'=>$address['mobile'],
            'province'=>$address['province'],
            'city' =>$address['city'],
            'area'=>$address['area'],
            'address' =>$address['address'],
            'create_time'=>$time,
            'pay_time'=>0,
            'express_no' =>'',
            'express_code'=>'',
            'type'=>$ordertype,
        );
        if(is_array($extdata)){
            foreach ($extdata as $k=>$val){
                if(!isset($orderdata[$k]) && !in_array($k,['status','rebated','total_price','total_postage'])){
                    $orderdata[$k]=$val;
                }
            }
        }else{
            $orderdata['remark']=$extdata;
        }
        try{
            Log::record("创建订单：".var_export($orderdata,true));
            $result= $this->insert($orderdata,true);
        }catch(\Exception $e){
            $this->rollback();
            $this->setError($e->getMessage());
            return false;
        }
        if($result){
            $i=0;
            foreach ($products as $product){
                $product['order_id']=$result;
                ProductModel::setFlash($product['product_id'],$time);
                OrderProductModel::create([
                    'order_id'=>$result,
                    'product_id'=>$product['product_id'],
                    'member_id'=>$member['id'],
                    'sku_id'=>$product['sku_id'],
                    'sku_specs'=>ProductModel::transSpec($product['specs']),
                    'product_title'=>$product['product_title'],
                    'product_image'=>$product['product_image'],
                    'product_orig_price'=>$product['product_price'],
                    'product_cost_price'=>$product['product_cost_price'],
                    'product_price'=>$product['release_price'],
                    'product_weight'=>$product['weight'],
                    'count'=>$product['count'],
                    'sort'=>$i++
                ]);
                //扣库存,加销量
                Db::name('ProductSku')->where('sku_id',$product['sku_id'])
                    ->dec('storage',$product['count'])
                    ->inc('sale',$product['count'])
                    ->update();
                Db::name('Product')->where('id',$product['product_id'])
                    ->dec('storage',$product['count'])
                    ->inc('sale',$product['count'])
                    ->update();
            }
            $this->commit();
            if($status>0 ){
                self::getInstance()->updateStatus(['status'=>$status,'pay_type'=>'balance','pay_time'=>time()],['order_id'=>$result]);
            }
        }else{
            $this->rollback();
            $this->setError("入单失败");
        }
        
        return $result;
    }
    
    public static function calc_size($size){
        if(!is_array($size)){
            $size=explode(',',$size);
        }
        if(count($size) < 3){
            return 0;
        }
        return $size[0]*$size[1]*$size[2];
    }

    public static function sendOrderMessage($order, $type, $products=null)
    {
        if(is_string($order) || is_numeric($order)){
            $order = Db::name('order')->where('order_id|order_no',$order)->find();
        }
        if(empty($order)){
            return false;
        }
        $fans = MemberOauthModel::where('member_id',$order['member_id'])->select();
        $msgdata=[];
        foreach ($fans as $fan){
            $wechat = WechatModel::where('id',$fan['type_id'])->find();
            if(empty($wechat['appid']) || empty($wechat['appsecret']))continue;
            $tplset = WechatTemplateMessageModel::getTpls($fan['type_id'],$type);
            if(empty($tplset) || empty($tplset['template_id']))continue;
    
            $tplset['keywords']=static::transkey($tplset['keywords']);
            
            if(empty($products)){
                $products=Db::name('orderProduct')->where('order_id',$order['order_id'])->select();
            }
            if(empty($msgdata)){
                if(!empty($order['appid'])){
                    $msgdata['appid']=$order['appid'];
                }
                $msgdata['order_no']=$order['order_no'];
                $msgdata['amount']=$order['payamount'];
                $goods=[];
                foreach ($products as $idx=>$product){
                    $goods[]=$product['product_title'];
                    if($idx>=1){
                        $goods[]='等'.array_sum(array_column($products,'count')).'件商品';
                        break;
                    }
                }
                
                $msgdata['goods']=implode('，',$goods);
                $msgdata['reason']=$order['reason'];
                $msgdata['create_date'] = date('Y-m-d H:i:s',$order['create_time']);
                $msgdata['pay_date'] = date('Y-m-d H:i:s',$order['pay_time']);
                $msgdata['confirm_date'] = date('Y-m-d H:i:s',$order['confirm_time']);
                $msgdata['status']=order_status($order['status'],false);
                if($order['status']<1){
                    $msgdata['pay_notice'] = '请在'.date('Y-m-d H:i:s',$order['create_time']+30*60).'前付款';
                }
                if($order['deliver_time']>0) {
                    $msgdata['deliver_date'] = date('Y-m-d H:i:s', $order['deliver_time']);
                    if(!empty($order['express_code'])){
                        $express=Db::name('expressCode')->where('express',$order['express_code'])->find();
                    }
                    if(empty($express)){
                        $msgdata['express'] = '无';
                    }else {
                        $msgdata['express'] = $express['name'];
                        $msgdata['express_no']=$order['express_no'];
                    }
                }
                $msgdata['page']='/pages/member/order-detail?id='.$order['order_id'];
                //$msgdata['url'] = url('/','',true,true).'?path=/member/order/detail?id='.$order['order_id'];
            }
            
            //小程序下如果未获得form_id，需要从支付信息中获取 prepay_id
            if($wechat['account_type'] == 'miniprogram' || $wechat['account_type'] == 'minigame'){
                $msgdata['form_id']=$order['form_id'];
                if(empty($msgdata['form_id'])){
                    $payorder = Db::name('payOrder')->where('member_id',$fan['member_id'])
                        ->where('order_type','order')->where('order_id',$order['order_id'])
                        ->where('status','>',0)
                        ->where('pay_id',$wechat['id'])->find();
                    if(!empty($payorder) && !empty($payorder['prepay_id'])){
                        $msgdata['form_id'] = $payorder['prepay_id'];
                    }else{
                        Log::record('支付信息未查询到');
                        continue;
                    }
                }
            }
    
            
            $return = WechatTemplateMessageModel::sendTplMessage($wechat,$tplset, $msgdata, $fan['openid']);
            if($return){
                return $return;
            }
            
        }
        return false;
    }
    
    
    /**
     * 根据设置或升级原则进行升级
     */
    public static function setLevel($order)
    {
        $member = Db::name('Member')->find($order['member_id']);
        if (!empty($member)) {
            $level_id=0;
            $ordertype = intval($order['type']);
            $levels = getMemberLevels();
            if(($ordertype & PRO_TYPE_BIND) == PRO_TYPE_BIND){
                $level_id=$order['level_id'];
            }elseif(($ordertype & PRO_TYPE_UPGRADE) == PRO_TYPE_UPGRADE){
            
                if ($member['level_id'] == 0) $member['level_id'] = getDefaultLevel();
                $level = $levels[$member['level_id']];
                if ($level['is_default']) {
                    foreach ($levels as $lv) {
                        if ($lv['level_price'] > $order['payamount']) {
                            break;
                        }
                        $level = $lv;
                    }
                    if ($level['is_default']) return;
                    $level_id=$level['level_id'];
                }
            }
            if($level_id) {
                if ($level_id != $member['level_id']) {
                    MemberModel::update(['level_id' => $level_id], ['id' => $order['member_id']]);
                } else {
                    //会员等级不变，但等级设置变了
                    if( $levels[$level_id]['is_agent']) {
                        MemberModel::checkAgent($member);
                    }
                }
            }
        }
    }

    public static function doRebate($order){
        if($order['rebated'] || !$order['member_id'])return false;
        $member=Db::name('Member')->where('id',$order['member_id'])->find();
    
        $total_rebate=0;
        if(!empty($member)) {
            $levels = getMemberLevels();
            $levelConfig = getLevelConfig($levels);
            $parents = getMemberParents($member['id'], $levelConfig['commission_layer'], false);
            $startself=getSetting('agent_start');
            if($member['is_agent'] && $startself==1){
                array_unshift($parents,$member);
            }
            
            if (!empty($parents)) {
                $pids = array_column($parents, 'id');
                Db::name('Member')->where('id', $member['referer'])->inc('recom_performance', $order['payamount'] * 100);
                Db::name('Member')->whereIn('id', $pids)->inc('total_performance', $order['payamount'] * 100);
    
                $specials = force_json_decode($order['commission_special']);
    
                for ($i = 0; $i < count($parents); $i++) {
                    $curLevel = $levels[$parents[$i]['level_id']];
                    $amount=0;
                    if ($order['commission_amount'] > 0 && $curLevel['commission_layer'] > $i && !empty($curLevel['commission_percent'][$i])) {
                        $curPercent = $curLevel['commission_percent'][$i];
                        $commission = $order['commission_amount'];
                        if ($curLevel['commission_limit'] && $commission > $curLevel['commission_limit']) {
                            $commission = $curLevel['commission_limit'];
                        }
                        $amount += $commission * $curPercent * .01;
                    }
                    
                    foreach ($specials as $special) {
                        $amount = 0;
                        if ($special['type'] == 3) {
                            $amount += ($special['amounts'][$i] ?: 0) * $special['count'];
                        } elseif ($special['type'] == 4) {
                            $amount += ($special['level_amounts'][$parents[$i]['level_id']][$i] ?: 0) * $special['count'];
                        } else {
                            if ($special['amount'] > 0 && !empty($special['percent'][$i])) {
                                $curPercent = floatVal($special['percent'][$i]);
                                $commission = $special['amount'] * 1;
                                if ($curLevel['commission_limit'] && $commission > $curLevel['commission_limit']) {
                                    $commission = $curLevel['commission_limit'];
                                }
                                $amount += $commission * $curPercent * .01;
                            }
                        }
                    }
                    $amount = round($amount,2);
                    if ($amount > 0) {
                        self::award_log($parents[$i]['id'], $amount, '消费分佣' . ($i + 1) . '代', 'commission', $order);
                        self::sendCommissionMessage($parents[$i], $member, $order, $amount, '消费分佣' . ($i + 1) . '代');
                        $total_rebate += $amount;
                    }
                }
            }
        }
        Db::name('Order')->where('order_id',$order['order_id'])
            ->update(['rebated'=>1,'rebate_time'=>time(),'rebate_total'=>$total_rebate]);
        return true;
    }
    public static function award_log($uid, $money, $reson, $type,$order,$field='reward')
    {
        $results=AwardLogModel::record($uid, $money, $type,$reson, $order,$field);

        //返奖同时可以处理其它

        return $results;
    }

    public static function sendCommissionMessage($member, $buyer, $order, $commission, $type = '佣金'){
        $message = getSetting('message_commission');
        if(!empty($message)){
            foreach([
                'username'=>MemberModel::showname($member),
                'userid'=>$member['id'],
                'buyer'=>MemberModel::showname($buyer),
                'amount'=>number_format($order['payamount'], 2),
                'type'=>$type,
                'commission'=>number_format($commission, 2)
            ] as $k=>$v){
                $message = str_replace("[$k]", $v, $message);
            }

            MessageService::sendWechatMessage($member['id'],$message);
        }
    }

    public function refund($order = null, $reason = '', $type = ''){
        if(empty($order)){
            $order = $this->getOrigin();
            $reason = $order['reason']?:'订单取消';
            $type = 'order-cancel';
        }
        if($order['pay_type'] == 'balance'){
            return money_log($order['member_id'], $order['payamount'] * 100,$reason,$type);
        }elseif($order['pay_type'] == 'offline'){
            return true;
        }else{
            return PayOrderModel::refund($order['order_id'],'order',$reason);
        }
    }

    public function comment($data, $order = null){
        if(empty($order)){
            $order = $this->getOrigin();
        }
        if(empty($order['order_id'])){
            throw new \Exception('order error');
        }
        
        foreach($data as $row){
            $row['order_id']=$order['order_id'];
            ProductCommentModel::create($row);
        }

        $this->updateStatus(['status'=>ORDER_STATUS_FINISH],['order_id'=>$order['order_id']]);

        return true;
    }

}