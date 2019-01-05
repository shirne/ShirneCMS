<?php

namespace app\common\model;


use think\Db;

class PayOrderModel extends BaseModel
{
    public static const PAY_TYPE_WECHAT='wechat';

    private static function create_no(){
        $maxid=static::field('max(id) as maxid')->find();
        $maxid = $maxid['maxid'];
        if(empty($maxid))$maxid=0;
        return date('YmdHis').self::pad_orderid($maxid+1,4);
    }
    private static function pad_orderid($id,$len=4){
        $strlen=strlen($id);
        return $strlen<$len?str_pad($id,$len,'0',STR_PAD_LEFT):substr($id,$strlen-$len);
    }

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