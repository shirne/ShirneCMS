<?php

namespace app\api\Controller;

use app\admin\model\MemberLevelModel;
use app\common\facade\MemberCartFacade;
use app\common\facade\OrderFacade;
use app\common\model\MemberOauthModel;
use app\common\model\OrderModel;
use app\common\model\WechatModel;
use app\common\validate\OrderValidate;
use EasyWeChat\Factory;
use think\Db;


/**
 * 订单操作
 * Class OrderController
 * @package app\api\Controller
 */
class OrderController extends AuthedController
{
    public function prepare(){
        $order_skus=$this->input['products'];
        $skuids=array_column($order_skus,'sku_id');
        $products=Db::view('ProductSku','*')
            ->view('Product',['title'=>'product_title','image'=>'product_image','levels','is_discount'],'ProductSku.product_id=Product.id','LEFT')
            ->whereIn('ProductSku.sku_id',idArr($skuids))
            ->select();

        return $this->response([
            'products'=>$products,
            'address'=>Db::name('MemberAddress')->where('member_id',$this->user['id'])->order('is_default DESC')->find(),
            'express'=>[
                'fee'=>0,
                'title'=>'快递免邮'
            ]
        ]);

    }
    public function confirm($from='quick'){
        $order_skus=$this->input['products'];
        if(empty($order_skus))$this->error('未选择下单商品');
        $sku_ids=array_column($order_skus,'sku_id');
        if($from=='cart'){
            $products=MemberCartFacade::getCart($this->user['id'],$sku_ids);
        }else{
            $products=Db::view('ProductSku','*')
                ->view('Product',['title'=>'product_title','spec_data','image'=>'product_image','levels','is_discount','is_commission','type'],'ProductSku.product_id=Product.id','LEFT')
                ->whereIn('ProductSku.sku_id',idArr($sku_ids))
                ->select();
            $counts=array_index($order_skus,'count,sku_id');
            $userLevel=MemberLevelModel::get($this->user['level_id']);
            foreach ($products as $k=>&$item){
                $item['product_price']=$item['price'];

                if($item['is_discount'] && $userLevel['discount']){
                    $item['product_price']=$item['product_price']*$userLevel['discount']*.01;
                }
                if(!empty($item['image']))$item['product_image']=$item['image'];
                if(isset($counts[$item['sku_id']])){
                    $item['count']=$counts[$item['sku_id']];
                }else{
                    $item['count']=1;
                }
                if(!empty($item['levels'])){
                    $levels=json_decode($item['levels'],true);
                    if(!in_array($this->user['level_id'],$levels)){
                        $this->error('您当前会员组不允许购买商品['.$item['product_title'].']');
                    }
                }
            }
            unset($item);
        }

        $total_price=0;
        $ordertype=1;
        foreach ($products as $item){
            $total_price += $item['product_price']*$item['count'];
            if($item['type']==2){
                $ordertype=2;
            }
        }
        //todo 邮费模板

        if($total_price != $this->input['total_price']){
            $this->error('下单商品价格已变动');
        }

        $data=$this->request->only('address_id,pay_type,remark,form_id','put');

        $validate=new OrderValidate();
        if(!$validate->check($data)){
            $this->error($validate->getError());
        }else{
            $address=Db::name('MemberAddress')->where('member_id',$this->user['id'])
                ->where('address_id',$data['address_id'])->find();
            $balancepay=$data['pay_type']=='balance'?1:0;

            $result=OrderFacade::makeOrder($this->user,$products,$address,$data['remark'],$balancepay,$ordertype);
            if($result){
                if($from=='cart'){
                    MemberCartFacade::delCart($sku_ids,$this->user['id']);
                }
                if($balancepay) {
                    return $this->response(['order_id',$result],1,'下单成功');
                }else{
                    $method=$data['pay_type'].'pay';
                    if(method_exists($this,$method)){
                        return $this->$method($result);
                    }else{
                        return $this->response(['order_id',$result],1,'下单成功，请尽快支付');
                    }

                }
            }else{
                $this->error('下单失败');
            }
        }
    }

    public function wechatpay($order_id){
        $order=OrderModel::get($order_id);
        if(empty($order) || $order['status']!=0){
            $this->error('订单已支付或不存在!',0,['order_id'=>$order_id]);
        }
        $wechat_id=$this->input['wechat_id'];
        $wechat=$wechat=Db::name('wechat')->where('type','wechat')
            ->where('id',$wechat_id)->find();
        if(empty($wechat)){
            $this->error('服务器配置错误',ERROR_LOGIN_FAILED,['order_id'=>$order_id]);
        }
        $userauth=MemberOauthModel::where('member_id',$this->user['id'])
            ->where('type_id',$wechat_id)
            ->where('type','wechat')
            ->find();
        if(empty($userauth)){
            $this->error('需要用户授权openid',ERROR_NEED_OPENID,['order_id'=>$order_id]);
        }
        $config=WechatModel::to_pay_config($wechat);

        $app = Factory::payment($config);

        $result = $app->order->unify([
            'body' => '订单-'.$order['order_no'],
            'out_trade_no' => $order['order_no'],
            'total_fee' => $order['payamount']*100,
            //'spbill_create_ip' => '', // 可选，如不传该参数，SDK 将会自动获取相应 IP 地址
            'notify_url' => url('api/wechat/payresult','',true,true),
            'trade_type' => 'JSAPI',
            'openid' => $userauth['openid'],
        ]);
        if(empty($result) || $result['return_code']!='SUCCESS'){
            $this->error('支付发起失败',0,['order_id'=>$order_id]);
        }

        $params=[
            'appId'=>$result['appid'],
            'timeStamp'=>time(),
            'nonceStr'=>$result['nonce_str'],
            'package'=>'prepay_id='.$result['prepay_id'],
            'signType'=>'MD5'
        ];
        ksort($params);
        $string=$this->ToUrlParams($params)."&key=".$config['key'];
        $params['paySign']=strtoupper(md5($string));

        return $this->response(['payment'=>$params,'order_id'=>$order_id]);
    }
    public function balancepay($order_id){
        $order=OrderModel::get($order_id);
        if(empty($order)|| $order['status']!=0){
            $this->error('订单已支付或不存在!',0,['order_id'=>$order_id]);
        }
        $debit = money_log($order['member_id'], -$order['payamount']*100, "下单支付", 'consume',0,'money');
        if ($debit){
            $order->save(['status'=>1,'pay_time'=>time()]);
            $this->success('支付成功!',1,['order_id'=>$order_id]);
        }
        $this->error('支付失败!',0,['order_id'=>$order_id]);
    }
    protected function ToUrlParams($arr)
    {
        $buff = "";
        foreach ($arr as $k => $v)
        {
            if($k != "sign" && $v != "" && !is_array($v)){
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }
}