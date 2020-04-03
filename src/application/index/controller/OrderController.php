<?php
/**
 * 订单功能
 * User: shirne
 * Date: 2018/5/13
 * Time: 23:57
 */

namespace app\index\controller;


use app\common\facade\MemberCartFacade;
//use app\common\facade\OrderFacade;
use app\common\model\CreditOrderModel;
use app\common\model\MemberOauthModel;
use app\common\model\OrderModel;
use app\common\model\PayOrderModel;
use app\common\model\ProductModel;
use app\common\model\WechatModel;
use app\common\validate\OrderValidate;
use EasyWeChat\Factory;
use think\Db;

class OrderController extends AuthedController
{
    public function initialize()
    {
        parent::initialize();
        $this->assign('navmodel','product');
    }

    /**
     * 确认下单
     * @param $sku_ids string
     * @param $count int
     * @param  $from string
     * @return \think\Response
     */
    public function confirm($sku_ids,$count=1,$from='quick')
    {
        if($from=='cart'){
            $products=MemberCartFacade::getCart($this->userid,$sku_ids);
        }else{
            $skucounts = array_combine_cmp(idArr($sku_ids),idArr($count),COMBINE_PAD_VALUE,1);
            $products = ProductModel::getForOrder($skucounts);
        }

        $total_price=0;
        foreach ($products as $item){
            $total_price += $item['product_price']*$item['count'];
        }

        if($this->request->isPost()){
            $data=$this->request->only('address_id,remark,pay_type,total_price','post');
            $validate=new OrderValidate();
            if(!$validate->check($data)){
                $this->error($validate->getError());
            }else{
                $address=Db::name('MemberAddress')->where('member_id',$this->userid)
                    ->where('address_id',$data['address_id'])->find();
                $balancepay=$data['pay_type']=='balance'?1:0;
                $remark=[
                    'remark'=>$data['remark'],
                    'platform'=>'web',
                    'total_price'=>$data['total_price']
                ];
                $orderModel=new OrderModel();
                $result=$orderModel->makeOrder($this->user,$products,$address,$remark,$balancepay);
                if($result){
                    if($from=='cart'){
                        MemberCartFacade::delCart($sku_ids,$this->userid);
                    }
                    if($balancepay) {
                        $this->success('下单成功');
                    }else{
                        $method=$data['pay_type'].'pay';
                        if(method_exists($this,$method)) {
                            $this->success('下单成功，即将跳转到支付页面', url('index/order/' . $method, ['order_id' => $result]));
                        }else{
                            $this->success('下单成功，请尽快支付');
                        }
                    }
                }else{
                    $this->error('下单失败:'.$orderModel->getError());
                }
            }
        }
        if(empty($products)){
            $this->error('产品不存在');
        }

        $addresses=Db::name('MemberAddress')->where('member_id',$this->userid)
            ->select();
        $this->assign('from',$from);
        $this->assign('addresses',$addresses);
        $this->assign('total_price',$total_price);
        $this->assign('products',$products);
        return $this->fetch();
    }

    public function wechatpay($order_id, $trade_type='JSAPI', $payid=0){
        $trade_type = strtoupper($trade_type);
        if($trade_type == 'JSAPI' ) {
            if (!$this->isWechat) {
                $this->error('请在微信内使用此支付方式!');
            }
            
            if(empty($this->wechatUser) ||($payid!=0 && $payid!=$this->wechatUser['type_id'])){
                $this->wechatUser = MemberOauthModel::where('type_id',$payid)->where('member_id',$this->userid)->find();
                //redirect()->remember();
                //redirect(url('index/order/wechatpay',['type'=>$payid]))->send();exit;
                if(empty($this->wechatUser))$this->error('支付方式错误');
            }
            
        }
        if($payid == 0 && !empty($this->wechatUser))$payid = $this->wechatUser['type_id'];
        $wechat=WechatModel::where('id',$payid)
            ->where('type','wechat')->find();
        if(empty($wechat)){
            $this->error('支付方式错误');
        }
        $config=WechatModel::to_pay_config($wechat);
        
        $paymodel = PayOrderModel::getInstance();
        $payorder = $paymodel->createFromOrder($wechat['id'],PayOrderModel::PAY_TYPE_WECHAT,$order_id,$trade_type);
        if(empty($payorder)){
            $this->error($paymodel->getError());
        }

        

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
        if(empty($result) || $result['return_code']!='SUCCESS' || $result['result_code']!='SUCCESS'){
            $this->error('支付发起失败');
        }
        if($trade_type == 'NATIVE'){
            $this->success('','',['code_url'=>$result['code_url']]);
        }
        if($trade_type == 'MWEB'){
            $this->success('',$result['mweb_url']);
        }

        $this->assign('paydata',$payorder->getSignedData($result,$config['key']));
        $this->assign('payorder',$payorder);
        return $this->fetch();
    }
    public function balancepay($order_id){
        $order=OrderModel::get($order_id);
        if(empty($order)|| $order['status']!=0){
            $this->error('订单已支付或不存在!');
        }
        $debit = money_log($order['member_id'], -$order['payamount']*100, "下单支付", 'consume',0,'money');
        if ($debit){
            $order->save(['status'=>1,'pay_type'=>'balance','pay_time'=>time()]);
            $this->success('支付成功!');
        }
        $this->error('支付失败!');
    }
    
}