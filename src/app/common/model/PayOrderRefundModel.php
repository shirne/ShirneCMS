<?php

namespace app\common\model;

use EasyWeChat\Factory;
use app\common\core\BaseModel;
use think\facade\Db;

/**
 * Class PayOrderRefundModel
 * @package app\common\model
 */
class PayOrderRefundModel extends BaseModel
{
    protected $name = 'pay_order_refund';
    const PAY_TYPE_WECHAT='wechat';
    const PAY_TYPE_ALIPAY='alipay';
    

    private static function create_no(){
        $maxid=Db::name('payOrderRefund')->max('id');
        if(empty($maxid))$maxid=0;
        return date('YmdHis').self::pad_orderid($maxid+1,4);
    }
    private static function pad_orderid($id,$len=4){
        $strlen=strlen($id);
        return $strlen<$len?str_pad($id,$len,'0',STR_PAD_LEFT):substr($id,$strlen-$len);
    }
    
    public function createFromPayOrder($payOrder, $reason, $refundFee = null){
        $refund = [
            'member_id'=>$payOrder['member_id'],
            'order_id'=>$payOrder['id'],
            'refund_no'=>static::create_no(),
            'refund_fee'=>$refundFee?$refundFee:($payOrder['pay_amount']*0.01),
            'status'=>0,
            'reason'=>$reason,
            'create_time'=>time(),
            'update_time'=>time()
        ];
        return Db::name('payOrderRefund')->insert($refund,true);
    }


    protected function triggerStatus($item, $status, $newData=[])
    {
        parent::triggerStatus($item, $status, $newData);
        if($status==1 ){
            
            //todo
        }elseif($status < 0){
            Db::name('payOrder')->where('id',$this['order_id'])->update(['is_refund'=>['DEC',1],'refund_fee'=>['DEC',$this['refund_fee']]]);
        }
    }
    
    public function checkStatus(){
        if(!$this['id'] || $this['status']!=0){
            $this->setError('当前状态错误');
            return false;
        }

        if(!$this['pay_id']){
            $this->setError('支付信息错误');
            return false;
        }

        if($this['pay_type']=='wechat'){
            $app = WechatModel::createApp($this['pay_id'],true);
            
            $result=$app->refund->queryByOutRefundNumber($this['refund_no']);
            if($result['return_code']=='SUCCESS' && $result['result_code']=='SUCCESS'){
                if($result['trade_state']=='SUCCESS'){
                    $this->updateStatus([
                        'status'=>1,
                        'refund_time'=>strtotime($result['success_time']),
                        'refund_result'=>$result['refund_recv_accout'],
                        'update_time'=>time(),
                    ]);
                }else{
                    $this->updateStatus(['status'=>-1,'refund_result'=>$result['refund_status']]);
                    $this->setError($result['trade_state_desc']);
                }
            }else{
                $this->setError( $result['err_code_des']?:$result['return_msg']);
                return false;
            }
        }

        return true;
    }


}