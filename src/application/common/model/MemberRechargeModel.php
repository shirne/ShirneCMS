<?php

namespace app\common\model;


use app\common\core\BaseModel;

class MemberRechargeModel extends BaseModel
{
    public function onPayResult($paytype, $paytime, $payamount){
        $this->updateStatus([
            'status'=>1,
            'audit_time'=>$paytime
        ]);
    }

    protected function triggerStatus($item, $status, $newData=[])
    {
        parent::triggerStatus($item, $status, $newData);
        if($status==1){
            money_log($item['member_id'],$item['amount'],'订单['.$item['id'].']充值成功','recharge');
        }
    }
}