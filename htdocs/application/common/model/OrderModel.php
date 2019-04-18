<?php

namespace app\common\model;


use think\Db;
use shirne\third\KdExpress;

/**
 * Class OrderModel
 * @package app\common\model
 */
class OrderModel extends BaseModel
{
    protected $pk='order_id';
    
    private function create_no(){
        $maxid=$this->field('max(order_id) as maxid')->find();
        $maxid = $maxid['maxid'];
        if(empty($maxid))$maxid=0;
        return date('YmdHis').$this->pad_orderid($maxid+1,4);
    }
    private function pad_orderid($id,$len=4){
        $strlen=strlen($id);
        return $strlen<$len?str_pad($id,$len,'0',STR_PAD_LEFT):substr($id,$strlen-$len);
    }

    public static function init()
    {
        parent::init();
        self::afterWrite(function ( $model)
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
                    }elseif($order['status']<0 && $order['cancel_time']==0){
                        $products=Db::name('orderProduct')->where('order_id',$order['order_id'])->select();
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
                        Db::name('Order')->where('order_id',$order['order_id'])
                            ->update(['cancel_time'=>time()]);
                    }
                }
            }
        });
    }

    /**
     * @param $member
     * @param $products
     * @param $address
     * @param $remark
     * @param $balance_pay
     * @param $ordertype
     * @return mixed
     */

    public function makeOrder($member,$products,$address,$remark,$balance_pay=1,$ordertype=1){

        //折扣
        $levels=getMemberLevels();
        $level=$levels[$member['level_id']];
        $discount=1;
        if(!empty($level) && $level['discount']<100){
            $discount = $level['discount']*.01;
        }

        //status 0-待付款 1-已付款
        $status=0;
        $total_price=0;
        $commission_amount=0;
        $comm_special = [];
        foreach ($products as $k=>$product){
            if($product['storage']<$product['count']){
                $this->error='商品['.$product['product_title'].']库存不足';
                return false;
            }
            if($product['count']<1){
                $this->error='商品['.$product['product_title'].']数量错误';
                return false;
            }

            $price=intval($product['product_price']*100) * $product['count'];
            if($product['is_discount']){
                $price*=$discount;
            }
            $total_price += $price;

            if($product['is_commission'] == 1 ){
                $cost_price=intval($product['cost_price']*100)* $product['count'];
                if($price>$cost_price) {
                    $commission_amount += $price - $cost_price;
                }
            }elseif($product['is_commission'] == 2){
                $cost_price=intval($product['cost_price']*100)* $product['count'];
                if($price>$cost_price) {
                    $comm_special[]=[
                        'amount'=> ($price - $cost_price)*.01,
                        'percent'=>force_json_decode($product['commission_percent'])
                    ];
                }
            }
        }

        //todo  优惠券

        
        $this->startTrans();

        if($balance_pay) {
            $debit = money_log($member['id'], -$total_price, "下单支付", 'consume',0,is_string($balance_pay)?$balance_pay:'money');
            if ($debit) $status = 1;
            else{
                $this->error="余额不足";
                return false;
            }
        }
        $time=time();
        $orderdata=array(
            'order_no'=>$this->create_no(),
            'member_id'=>$member['id'],
            'level_id'=>0,
            'payamount'=>$total_price*.01,
            'commission_amount'=>$commission_amount*.01,
            'commission_special'=>json_encode($comm_special),
            'status'=>0,
            'isaudit'=>getSetting('autoaudit')==1?1:0,
            'remark'=>$remark,
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
        $result= $this->insert($orderdata,false,true);

        if($result){
            $i=0;
            foreach ($products as $product){
                $product['order_id']=$result;
                $release_price=$product['product_price'];
                if($product['is_discount']){
                    $release_price *= $discount;
                }
                Db::name('orderProduct')->insert([
                    'order_id'=>$result,
                    'product_id'=>$product['product_id'],
                    'sku_id'=>$product['sku_id'],
                    'sku_specs'=>ProductModel::transSpec($product['specs']),
                    'product_title'=>$product['product_title'],
                    'product_image'=>$product['product_image'],
                    'product_orig_price'=>$product['product_price'],
                    'product_price'=>$release_price,
                    'product_weight'=>$product['product_weight'],
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
        }else{
            $this->error = "入单失败";
            $this->rollback();
        }
        if($status>0 ){
            self::update(['status'=>$status,'pay_time'=>time()],['order_id'=>$result]);
        }
        return $result;
    }

    /**
     * 根据设置或升级原则进行升级
     */
    public static function setLevel($order){
        if($order['type']==2){
            $member=Db::name('Member')->find($order['member_id']);
            $levels=getMemberLevels();
            if(!empty($member)){
                if($member['level_id']==0)$member['level_id']=getDefaultLevel();
                $level=$levels[$member['level_id']];
                if($level['is_default']){
                    foreach ($levels as $lv){
                        if($lv['level_price'] > $order['payamount']){
                            break;
                        }
                        $level=$lv;
                    }
                    if($level['is_default'])return;
                    MemberModel::update(['level_id'=>$level['level_id']],['id'=>$order['member_id']]);
                }
            }
        }
    }

    public static function doRebate($order){
        if($order['rebated'] || !$order['member_id'])return false;
        $member=Db::name('Member')->where('id',$order['member_id'])->find();
        if(empty($member))return true;
        $levels=getMemberLevels();
        $levelConfig=getLevelConfig($levels);
        $parents=getMemberParents($member['id'],$levelConfig['commission_layer'],false);
        if(empty($parents))return true;

        $pids=array_column($parents,'id');
        Db::name('Member')->where('id', $member['referer'])->setInc('recom_performance', $order['payamount'] * 100);
        Db::name('Member')->whereIn('id', $pids)->setInc('total_performance', $order['payamount'] * 100);

        $specials = force_json_decode($order['commission_special']);

        for ($i = 0; $i < count($parents); $i++) {
            $curLevel=$levels[$parents[$i]['level_id']];
            if($curLevel['commission_layer']>$i && !empty($curLevel['commission_percent'][$i])) {
                $curPercent = $curLevel['commission_percent'][$i];
                $commission = $order['commission_amount'];
                if($curLevel['commission_limit'] && $commission>$curLevel['commission_limit']){
                    $commission = $curLevel['commission_limit'];
                }
                $amount = $commission * $curPercent * .01;
                self::award_log($parents[$i]['id'],$amount,'消费分佣'.($i+1).'代','commission',$order);
            }

            foreach ($specials as $special){
                if($special['amount'] > 0 && !empty($special['percent'][$i])){
                    $curPercent = floatVal($special['percent'][$i]);
                    $commission = $special['amount'] * 1;
                    if($curLevel['commission_limit'] && $commission>$curLevel['commission_limit']){
                        $commission = $curLevel['commission_limit'];
                    }
                    $amount = $commission * $curPercent * .01;
                    self::award_log($parents[$i]['id'], $amount, '消费分佣' . ($i + 1) . '代', 'commission', $order);
                }
            }
        }
        return true;
    }
    public static function award_log($uid, $money, $reson, $type,$order,$field='credit')
    {
        $amount=$money*100;
        $results=AwardLogModel::record($uid, $amount, $type,$reson, $order,$field);

        //返奖同时可以处理其它

        return $results;
    }

     /**
     * @param bool $force
     * @return array
     */
    public function fetchExpress($force=false)
    {
        if($force || $this->express_time<time()-3600)
        {
            if(!$force && !empty($this->express_data)){
                $express=json_decode($this->express_data);
                if($express['Success']==true && $express['State']==3){
                    return $express;
                }
            }
            if(!empty($this->express_no) && !empty($this->express_code)) {
                $express = new KdExpress([
                    'appid'=>getSetting('kd_userid'),
                    'appsecret'=>getSetting('kd_apikey')
                ]);
                $data = $express->QueryExpressTraces($this->express_code, $this->express_no);
                $this->express_data=$data;
                $this->express_time=time();
                $this->save();
            }
        }
        return empty($this->express_data)?[]:json_decode($this->express_data,TRUE);
    }
}