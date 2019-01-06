<?php

namespace app\common\model;


use think\Db;

/**
 * Class PayOrderModel
 * @package app\common\model
 */
class PayOrderModel extends BaseModel
{
    public const PAY_TYPE_WECHAT='wechat';
    public const PAY_TYPE_ALIPAY='alipay';

    private static function create_no(){
        $maxid=Db::name('payOrder')->max('id');
        if(empty($maxid))$maxid=0;
        return date('YmdHis').self::pad_orderid($maxid+1,4);
    }
    private static function pad_orderid($id,$len=4){
        $strlen=strlen($id);
        return $strlen<$len?str_pad($id,$len,'0',STR_PAD_LEFT):substr($id,$strlen-$len);
    }

    /**
     * 创建支付单
     * @param $paytype string 支付类型
     * @param $type string 订单类型 order/recharge
     * @param $order_id int 订单表的id
     * @param $amount int 金额(分)
     * @param $member_id int 会员id
     * @return static
     */
    public static function createOrder($paytype,$type,$order_id,$amount,$member_id){
        return static::create([
            'member_id'=>$member_id,
            'pay_type'=>$paytype,
            'order_no'=>static::create_no(),
            'order_type'=>$type,
            'order_id'=>$order_id,
            'create_time'=>time(),
            'pay_amount'=>$amount*100
        ]);
    }

    protected function triggerStatus($item, $status)
    {
        parent::triggerStatus($item, $status);
        if($status==1){
            switch ($item['order_type']){
                case 'recharge':
                    MemberRechargeModel::updateStatus([
                        'status'=>1,
                        'audit_time'=>$item['pay_time']
                    ],['id'=>$item['order_id']]);
                    break;
                default:
                    OrderModel::update([
                        'status'=>1,
                        'pay_time'=>$item['pay_time']
                    ],['order_id'=>$item['order_id']]);
                    break;
            }
        }
    }

}