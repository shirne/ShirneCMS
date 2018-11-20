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
    public function confirm($sku_ids,$count=1,$from='quick'){
        if($from=='cart'){
            $products=MemberCartFacade::getCart($this->user['id'],$sku_ids);
        }else{
            $products=Db::view('ProductSku','*')
                ->view('Product',['title'=>'product_title','image'=>'product_image','levels','is_discount'],'ProductSku.product_id=Product.id','LEFT')
                ->whereIn('ProductSku.sku_id',idArr($sku_ids))
                ->select();
            $counts=idArr($count);
            $userLevel=MemberLevelModel::get($this->user['level_id']);
            foreach ($products as $k=>&$item){
                $item['product_price']=$item['price'];

                if($item['is_discount'] && $userLevel['discount']){
                    $item['product_price']=$item['product_price']*$userLevel['discount']*.01;
                }
                if(!empty($item['image']))$item['product_image']=$item['image'];
                if(isset($counts[$k])){
                    $item['count']=$counts[$k];
                }else{
                    $item['count']=$counts[0];
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
        foreach ($products as $item){
            $total_price += $item['product_price']*$item['count'];
        }
        $data=$this->request->only('address_id,pay_type,remark','input');
        $validate=new OrderValidate();
        if(!$validate->check($data)){
            $this->error($validate->getError());
        }else{
            $address=Db::name('MemberAddress')->where('member_id',$this->user['id'])
                ->where('address_id',$data['address_id'])->find();
            $balancepay=$data['pay_type']=='balance'?1:0;
            $result=OrderFacade::makeOrder($this->user,$products,$address,$data['remark'],$balancepay);
            if($result){
                if($from=='cart'){
                    MemberCartFacade::delCart($sku_ids,$this->user['id']);
                }
                if($balancepay) {
                    $this->success('下单成功');
                }else{
                    $method=$data['pay_type'].'pay';
                    if(method_exists($this,$method)){
                        return $this->$method($result);
                    }else{
                        $this->success('下单成功，请尽快支付');
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
            $this->error('订单已支付或不存在!');
        }
        $wechat_id=$this->input['wechat_id'];
        $wechat=$wechat=Db::name('wechat')->where('type','wechat')
            ->where('id',$wechat_id)->find();
        if(empty($wechat)){
            $this->error('服务器配置错误',ERROR_LOGIN_FAILED);
        }
        $userauth=MemberOauthModel::where('member_id',$this->user['id'])
            ->where('type_id',$wechat_id)
            ->where('type','wechat')
            ->find();
        if(empty($userauth)){
            $this->error('需要用户授权openid',ERROR_NEED_OPENID);
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
            $this->error('支付发起失败');
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

        return $this->response($params);
    }
    public function balancepay($order_id){
        $order=OrderModel::get($order_id);
        if(empty($order)|| $order['status']!=0){
            $this->error('订单已支付或不存在!');
        }
        $debit = money_log($order['member_id'], -$order['payamount']*100, "下单支付", 'consume','money');
        if ($debit){
            $order->save(['status'=>1,'pay_time'=>time()]);
            $this->success('支付成功!');
        }
        $this->error('支付失败!');
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