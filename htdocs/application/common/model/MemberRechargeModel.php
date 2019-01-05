<?php

namespace app\common\model;


class MemberRechargeModel extends BaseModel
{

    protected function triggerStatus($item, $status)
    {
        parent::triggerStatus($item, $status);
        if($status==1){
            money_log($item['member_id'],$item['amount'],'订单['.$item['id'].']充值成功','recharge');
        }
    }
}