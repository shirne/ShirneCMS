<?php

namespace app\common\model;

use app\common\core\BaseModel;
use think\Db;

define('REFUND_TYPE_RETURN', 1);
define('REFUND_TYPE_NORETURN', 2);
define('REFUND_TYPE_EXCHANGE', 3);

class OrderRefundModel extends BaseModel
{
    protected $name = 'order_refund';
    protected $autoWriteTimestamp = true;
    protected $type = ['product'=>'array','address'=>'array','express'=>'array'];

    public static function createRefund($order, $form){
        if($order['status'] < 1){
            throw new \Exception('订单状态错误');
        }
        if($order['islock'] < 1){
            throw new \Exception('订单不可退款或已过退款期');
        }
        if($order['refund_type'] > 0){
            throw new \Exception('订单已有退款');
        }
        $data = [
            'type'=>$form['type'],
            'member_id'=>$form['member_id'],
            'order_id' => $order['order_id'],
            'type' => $form['type'],
            'reason' => $form['reason'],
            'remark' => $form['remark'],
            'amount' => $form['amount'],
            'image' => $form['image']
        ];
        if($data['type'] == REFUND_TYPE_RETURN || $data['type'] == REFUND_TYPE_NORETURN){
            if($data['amount'] > $order['payamount']){
                throw new \Exception('退款金额不能大于订单支付金额');
            }
        }

        $data['order_id'] = $order['order_id'];

        try{
            $model = self::create($data);
            Db::name('order')->where('order_id',$order['order_id'])->update(['refund_type'=>$data['type'],'reason'=>$data['reason']]);
        }catch(\Exception $e){
            return false;
        }
        return $model;
    }
}