<?php

namespace app\common\model;


use think\Db;

/**
 * Class PayOrderModel
 * @package app\common\model
 */
class PayOrderModel extends BaseModel
{
    const PAY_TYPE_WECHAT='wechat';
    const PAY_TYPE_ALIPAY='alipay';
    protected $type = ['pay_data'=>'array'];

    private static function create_no(){
        $maxid=Db::name('payOrder')->max('id');
        if(empty($maxid))$maxid=0;
        return date('YmdHis').self::pad_orderid($maxid+1,4);
    }
    private static function pad_orderid($id,$len=4){
        $strlen=strlen($id);
        return $strlen<$len?str_pad($id,$len,'0',STR_PAD_LEFT):substr($id,$strlen-$len);
    }
    
    public function createFromOrder($payid, $paytype, $orderno, $trade_type=''){
        $ordertype='';
        $orderid=0;
        if(strpos($orderno,'CZ_')===0){
            $ordertype='recharge';
            $orderno=intval(substr($orderno,3));
            $order=Db::name('memberRecharge')->where('id',$orderno)
                ->find();
            if(!empty($order)) {
                $order['payamount'] = $order['amount'] * .01;
                $order['order_no'] = 'CZ_' . str_pad($order['id'], 6, '0', STR_PAD_LEFT);
                $orderid = $order['id'];
            }
        }elseif(strpos($orderno,'PO_')===0){
            $ordertype = 'credit';
            $orderno = intval(substr($orderno, 3));
            $order = CreditOrderModel::get($orderno);
            if(!empty($order)) {
                $orderid = $order['id'];
            }
        }else {
            $order = OrderModel::get($orderno);
            if(!empty($order)) {
                $orderid = $order['order_id'];
            }
        }
        
        if(empty($order) || $order['status']<0){
            $this->setError('订单已失效或不存在!');
            return false;
        }
        
        if($order['status']>0){
            $this->setError('订单已支付!',8);
            return false;
        }
        
        if($order['payamount'] <= 0){
            $this->triggerStatus(['order_type'=>$ordertype,'order_id'=>$orderid,'pay_time'=>time()],1);
            $this->setError('订单支付成功!',9);
        
            return false;
        }
        
        return self::createOrder(
            $paytype,$payid,
            $ordertype,$orderid,$order['payamount'],$order['member_id'],$trade_type
        );
    }

    /**
     * 创建支付单
     * @param $paytype string 支付类型
     * @param $payid int 支付号
     * @param $type string 订单类型 order/recharge
     * @param $order_id int 订单表的id
     * @param $amount int 金额(分)
     * @param $member_id int 会员id
     * @param $trade_type string 交易类型
     * @param $data
     * @return static
     */
    public static function createOrder($paytype,$payid,$type,$order_id,$amount,$member_id,$trade_type='',$data=[]){
        
        return static::create([
            'member_id'=>$member_id,
            'pay_type'=>$paytype,
            'pay_id'=>$payid,
            'trade_type'=>$trade_type,
            'order_no'=>static::create_no(),
            'order_type'=>$type,
            'order_id'=>$order_id,
            'create_time'=>time(),
            'pay_data'=>$data,
            'pay_amount'=>intval($amount*100)
        ]);
    }

    protected function triggerStatus($item, $status, $newData=[])
    {
        parent::triggerStatus($item, $status, $newData);
        if($status==1 ){
            switch ($item['order_type']){
                case 'recharge':
                    MemberRechargeModel::getInstance()->updateStatus([
                        'status'=>1,
                        'audit_time'=>$item['pay_time']
                    ],['id'=>$item['order_id']]);
                    break;
                case 'credit':
                    CreditOrderModel::getInstance()->updateStatus([
                        'status'=>1,
                        'pay_time'=>$item['pay_time']
                    ],['order_id'=>$item['order_id']]);
                    break;
                default:
                    OrderModel::getInstance()->updateStatus([
                        'status'=>1,
                        'pay_time'=>$item['pay_time']
                    ],['order_id'=>$item['order_id']]);
                    break;
            }
        }
    }
    
    public function getSignedData($result, $key){
        $params=[
            'appId'=>$result['appid'],
            'timeStamp'=>time(),
            'nonceStr'=>$result['nonce_str'],
            'package'=>'prepay_id='.$result['prepay_id'],
            'signType'=>'MD5'
        ];
        ksort($params);
        $string=$this->toUrlParams($params)."&key=".$key;
        $params['paySign']=strtoupper(md5($string));
        return $params;
    }
    
    protected function toUrlParams($arr)
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