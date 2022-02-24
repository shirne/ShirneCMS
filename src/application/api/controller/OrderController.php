<?php

namespace app\api\controller;


use app\common\facade\MemberCartFacade;
//use app\common\facade\OrderFacade;
use app\common\model\OrderModel;
use app\common\model\PayOrderModel;
use app\common\model\PostageModel;
use app\common\model\ProductModel;
use app\common\model\WechatModel;
use app\common\validate\OrderValidate;
use EasyWeChat\Factory;
use think\facade\Log;
use think\Db;

/**
 * 订单操作
 * Class OrderController
 * @package app\api\Controller
 */
class OrderController extends AuthedController
{
    /**
     * 初始化订单信息
     * @param string $from 下单来源，购物车或直接下单
     * @param array $goods 需要购买的商品列表，每个item包含sku_id 和count,count默认1
     * @return Json 
     */
    public function prepare($from='quick'){
        $order_skus=$this->request->param('products');
        $address=$this->request->param('address');
        $skuids=array_column($order_skus,'sku_id');
        if($from == 'quick'){
            $skucounts = array_column($order_skus,'count','sku_id');
            $products = ProductModel::getForOrder($skucounts);
        }else{
            $products=MemberCartFacade::getCart($this->user['id'],$skuids);
        }
        foreach($products as $product){
            if(!empty($product['levels'])){
                if (!in_array($this->user['level_id'], $product['levels'])) {
                    $this->error('您当前会员组不允许购买商品[' . $product['product_title'] . ']');
                }
            }
        }
        $result=['products'=>$products];
        if(empty($address)){
            $address = Db::name('MemberAddress')->where('member_id',$this->user['id'])->order('is_default DESC')->find();
            $result['address']=$address;
        }elseif(!is_array($address)){
            $address = Db::name('MemberAddress')->where('member_id',$this->user['id'])->where('address_id',$address)->order('is_default DESC')->find();
            $result['address']=$address;
        }
        $result['express'] = PostageModel::calcolate($products,$address);
        return $this->response($result);

    }

    /**
     * 确认下单
     * @param string $from 下单来源，购物车或直接下单，购物车下单会移除下单成功的商品
     * @param array $goods 商品信息，每个包含sku_id和count count默认为1
     * @param int $address_id 收货地址id
     * @param string $pay_type 支付类型
     * @param string $remark 订单备注
     * @param string $form_id 小程序中下单可获取到form_id 用以发送模板消息
     * @return mixed 
     */
    public function confirm($from='quick'){
        $this->check_submit_rate();
        
        $order_skus=$this->request->param('products');
        if(empty($order_skus))$this->error('未选择下单商品');
        $sku_ids=array_column($order_skus,'sku_id');
        if($from=='cart'){
            $products=MemberCartFacade::getCart($this->user['id'],$sku_ids);
        }else{
            $skucounts = array_column($order_skus,'count','sku_id');
            $products=ProductModel::getForOrder($skucounts);
        }
        $products = array_column($products,NULL,'sku_id');
        foreach ($order_skus as $k=>$item){
            if(!isset($products[$item['sku_id']])){
                $this->error('部分商品已下架');
            }
            $products[$item['sku_id']]['postage_area_id']=$item['postage_area_id'];
            $order_skus[$k]= $products[$item['sku_id']];
        }
        
        //todo 邮费模板


        $data=$this->request->only('address_id,pay_type,remark,form_id,total_price,total_postage','put');

        $validate=new OrderValidate();
        if(!$validate->check($data)){
            $this->error($validate->getError());
        }else{
            $address=Db::name('MemberAddress')->where('member_id',$this->user['id'])
                ->where('address_id',$data['address_id'])->find();
            $balancepay=0;
            if(!empty($data['pay_type']) ){
                if($data['pay_type']=='balance' || $data['pay_type']=='money')$balancepay=1;

                if(!$balancepay){
                    $this->error('支付方式错误');
                }
            }
            if($balancepay){
                $secpassword=$this->request->param('secpassword');
                if(empty($secpassword)){
                    $this->error('请填写安全密码');
                }
                if(!compare_secpassword($this->user,$secpassword)){
                    $this->error('安全密码错误');
                }
            }

            $platform=$this->request->tokenData['platform']?:'';
            $appid=$this->request->tokenData['appid']?:'';
            $remark=[
                'remark'=>$data['remark'],
                'platform'=>$platform,
                'appid'=>$appid,
                'form_id'=>$data['form_id'],
                'total_price'=>$data['total_price'],
                'total_postage'=>$data['total_postage'],
            ];
            try{
                $orderModel=new OrderModel();
                $result=$orderModel->makeOrder($this->user,$order_skus,$address,$remark,$balancepay);
            }catch(\Exception $e){
                $this->error($e->getMessage());
            }
            if($result){
                if($from=='cart'){
                    MemberCartFacade::delCart($sku_ids,$this->user['id']);
                }
                if($balancepay) {
                    return $this->response(['order_id',$result],1,'下单成功');
                }else{
                    $method=isset($data['pay_type'])?($data['pay_type'].'pay'):'';
                    if($method && method_exists($this,$method)){
                        return $this->$method($result);
                    }else{
                        return $this->response(['order_id'=>$result],1,'下单成功，请尽快支付');
                    }

                }
            }else{
                $this->error('下单失败:'.$orderModel->getError());
            }
        }
    }

    public function wechatpay($order_id, $trade_type='JSAPI', $payid=0){
        $trade_type = strtoupper($trade_type);
        if($payid)$wechat=WechatModel::where(is_numeric($payid)?'id':'hash',$payid)->where('type','wechat')->find();
        if($trade_type == 'JSAPI' ) {
            if(empty($this->wechatUser) && !empty($wechat)){
                $openid = $this->request->param('openid');
                if($openid){
                    $this->wechatUser = Db::name('memberOauth')->where('openid',$openid)
                    ->where('type_id',$wechat['id'])->find();
                }else{
                    $this->wechatUser = Db::name('memberOauth')->where('member_id',$this->user['id'])
                    ->where('type_id',$wechat['id'])->find();
                }
            }
            if(!empty($this->wechatUser) && !$payid){
                $payid = $this->wechatUser['type_id'];
            }
            if(empty($this->wechatUser)){
                $this->error('用户未绑定微信号');
            }
        }
        if(empty($wechat) && $payid){
            $wechat=WechatModel::where(is_numeric($payid)?'id':'hash', $payid)
                ->where('type','wechat')->find();
        }
        if(empty($wechat)){
            $this->error('参数错误');
        }
    
        $paymodel = PayOrderModel::getInstance();
        $payorder = $paymodel->createFromOrder($wechat['id'],PayOrderModel::PAY_TYPE_WECHAT,$order_id,$trade_type);
        if(empty($payorder)){
            if(in_array($paymodel->getErrNo(),[8,9])){
                $this->success($paymodel->getError());
            }
            $this->error($paymodel->getError());
        }
    
        try{
            $config=WechatModel::to_pay_config($wechat);
        
            $app = Factory::payment($config);
        
            $result = $app->order->unify([
                'body' => '订单-'.$order_id,
                'out_trade_no' => $payorder['order_no'],
                'total_fee' => $payorder['pay_amount'],
                //'spbill_create_ip' => '', // 可选，如不传该参数，SDK 将会自动获取相应 IP 地址
                'notify_url' => url('api/wechat/payresult',['hash'=>$wechat['hash']],true,true),
                'trade_type' => $trade_type,
                'openid' => empty($this->wechatUser)?'':$this->wechatUser['openid'],
            ]);
        }catch(\Exception $e){
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            $this->error('支付发起失败');
        }
        if(empty($result) || $result['return_code']!='SUCCESS' || $result['result_code']!='SUCCESS'){
            Log::warning(json_encode($result,JSON_UNESCAPED_UNICODE));
            $this->error('支付发起失败');
        }
        $data=['payorder'=>$payorder];
        if($trade_type == 'NATIVE'){
            $data['code_url']=$result['code_url'];
        }
        if($trade_type == 'MWEB'){
            $data['mweb_url']=$result['mweb_url'];
        }
        if($trade_type == 'JSAPI'){
            $data['payment']=$payorder->getSignedData($result,$config['key']);
        }
        if(!empty($result['prepay_id'])){
            PayOrderModel::where('id',$payorder['id'])->update(['prepay_id'=>$result['prepay_id'],'appid'=>$wechat['appid']]);
        }
    
        return $this->response($data);
    }
    public function balancepay($order_id, $type='money'){
        if(!in_array($type,['money'])){
            $this->error('支付方式错误!');
        }
        $secpassword=$this->request->param('secpassword');
        if(empty($secpassword)){
            $this->error('请填写安全密码');
        }
        if(!compare_secpassword($this->user,$secpassword)){
            $this->error('安全密码错误');
        }
        $order=OrderModel::get($order_id);
        if(empty($order)|| $order['status']!=0){
            $this->error('订单已支付或不存在!',0,['order_id'=>$order_id]);
        }
        $debit = money_log($order['member_id'], -$order['payamount']*100, "下单支付", 'consume',0,'money');
        if ($debit){
            $order->save(['status'=>1,'pay_type'=>$type,'pay_time'=>time()]);
            $this->success(['order_id'=>$order_id], 1, '支付成功!');
        }
        $this->error('支付失败!',0,['order_id'=>$order_id]);
    }
}