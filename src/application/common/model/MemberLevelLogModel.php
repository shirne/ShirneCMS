<?php

namespace app\common\model;


use app\common\core\BaseModel;
use shirne\third\KdExpress;
use think\Db;

define('UPGRADE_STATUS_REFUND',-2);
define('UPGRADE_STATUS_CANCEL',-1);
define('UPGRADE_STATUS_UNPAIED',0);
define('UPGRADE_STATUS_PAIED',1);

class MemberLevelLogModel extends BaseModel
{
    protected $pk='order_id';
    protected $type = [];


    public static function init()
    {
        parent::init();
    }
    
    protected function triggerStatus($item, $status, $newData=[])
    {
        parent::triggerStatus($item, $status, $newData);
        if($status < 0){
            if($item['cancel_time']==0){
                Db::name('memberLevelLog')->where('id',$item['id'])
                    ->update(['cancel_time'=>time()]);
            }
        }else{
            if($status < $item['status'])return;
            switch ($status){
                case 1:
                    $this->afterPay($item);
                    break;
            }
        }
    }
    
    protected function afterPay($item=null){
        $member_id = $item?$item['member_id']:$this['member_id'];
        if($member_id>0){
            MemberModel::where('member_id',$member_id)->update(['level_id'=>$item['level_id']]);
        }
    }
    
    /**
     * @param $member
     * @param $goodss
     * @param $address
     * @param $paycredit
     * @param $remark
     * @param $balance_pay
     * @return mixed
     */
    public function makeOrder($member, $level, $remark, $balance_pay=0){

        //status 0-待付款 1-已付款
        $status=0;
        $price=$level['level_price'];


        $this->startTrans();

        if($price>0) {
            if ($balance_pay) {
                $debit = money_log($member['id'], -$price * 100, "升级订单", 'consume', 0, is_string($balance_pay) ? $balance_pay : 'money');
                if ($debit) $status = 1;
                else {
                    $this->error = lang('Balance')."不足";
                    $this->rollback();
                    return false;
                }
            }
        }else{
            $status = 1;
        }

        $time=time();
        $orderdata=array(
            'member_id'=>$this->user['id'],
            'level_id'=>$level['level_id'],
            'amount'=>$price,
            'create_time'=>$time,
            'status'=>0,
            'remark'=>$remark
        );

        $result= $this->insert($orderdata,false,true);

        if($result){
            $this->commit();
        }else{
            $this->error = "提交失败";
            $this->rollback();
        }
        if($status>0 ){
            self::updateStatus(['status'=>$status,'pay_time'=>time()],['id'=>$result]);
        }
        return $result;
    }


}