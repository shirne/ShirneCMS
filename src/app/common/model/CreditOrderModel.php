<?php

namespace app\common\model;


use app\common\core\BaseOrderModel;
use think\facade\Db;

define('CREDIT_STATUS_REFUND',-2);
define('CREDIT_STATUS_CANCEL',-1);
define('CREDIT_STATUS_UNPAIED',0);
define('CREDIT_STATUS_PAIED',1);
define('CREDIT_STATUS_SHIPED',2);
define('CREDIT_STATUS_RECEIVED',3);
define('CREDIT_STATUS_FINISH',4);

class CreditOrderModel extends BaseOrderModel
{
    protected $name = 'credit_order';
    protected $pk='order_id';
    protected $type = [];


    public static function getCounts($member_id=0){
        $model=Db::name('creditOrder')->where('delete_time',0);
        if($member_id>0){
            $model->where('member_id',$member_id);
        }
        $countlist=$model->group('status')->field('status,count(order_id) as order_count')->select();
        $counts=[0,0,0,0,0,0,0];
        foreach ($countlist as $row){
            $counts[$row['status']]=$row['order_count'];
        }
        return $counts;
    }
    
    protected function triggerStatus($item, $status, $newData=[])
    {
        parent::triggerStatus($item, $status, $newData);
        if($status < 0){
            if($item['cancel_time']==0){
                $goodss=Db::name('creditOrderGoods')->where('order_id',$item['order_id'])->select();
                foreach ($goodss as $goods) {
                    Db::name('Goods')->where('id', $goods['goods_id'])
                        ->inc('storage', $goods['count'])
                        ->dec('sale', $goods['count'])
                        ->update();
                }
                Db::name('creditOrder')->where('order_id',$item['order_id'])
                    ->update(['cancel_time'=>time()]);
            }
        }else{
            if($status < $item['status'])return;
            switch ($status){
                case 1:
                    $this->afterPay($item);
                    break;
                case 2:
                    $this->afterDeliver($item);
                    break;
                case 3:
                    $this->afterReceive($item);
                    break;
                case 4:
                    $this->afterComplete($item);
                    break;
            }
        }
    }
    
    protected function afterPay($item=null){
    
    }
    protected function afterDeliver($item=null){
    
    }
    protected function afterReceive($item=null){
    
    }
    protected function afterComplete($item=null){
    
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
    public function makeOrder($member,$goodss,$address,$paycredit,$remark,$balance_pay=1){

        //status 0-待付款 1-已付款
        $status=0;
        $total_price=0;
        foreach ($goodss as $k=>$goods){
            if($goods['storage']<$goods['count']){
                $this->error='商品['.$goods['title'].']库存不足';
                return false;
            }
            if($goods['count']<1){
                $this->error='商品['.$goods['title'].']数量错误';
                return false;
            }

            $price=intval($goods['price']*100) * $goods['count'];

            $total_price += $price;

        }


        $this->startTrans();
        if($paycredit){
            $paycredit = $paycredit * 100;
            $decpoints = money_log($member['id'], -$paycredit, lang('Credit')."支付", 'consume',0,'points');
            if(!$decpoints){
                $this->error=lang('Credit')."扣除失败";
                return false;
            }
        }

        $pay_price = $total_price - $paycredit;

        if($pay_price>0) {
            if ($balance_pay) {
                $debit = money_log($member['id'], -$pay_price , lang('Credit')."商品抵扣", 'consume', 0, is_string($balance_pay) ? $balance_pay : 'money');
                if ($debit) $status = 1;
                else {
                    $this->error = "余额不足";
                    $this->rollback();
                    return false;
                }
            }
        }else{
            $status = 1;
        }

        $time=time();
        $orderdata=array(
            'order_no'=>$this->create_no(),
            'member_id'=>$member['id'],
            'paycredit'=>$paycredit * .01,
            'payamount'=>$pay_price*.01,
            'status'=>0,
            'remark'=>$remark,
            'address_id'=>$address['address_id'],
            'recive_name'=>$address['recive_name'],
            'mobile'=>$address['mobile'],
            'province'=>$address['province'],
            'city' =>$address['city'],
            'area'=>$address['area'],
            'address' =>$address['address'],
            'create_time'=>$time,
            'pay_time'=>0,
            'express_no' =>'',
            'express_code'=>''
        );

        $result= $this->insert($orderdata,false,true);

        if($result){
            $i=0;
            foreach ($goodss as $goods){
                $goods['order_id']=$result;

                Db::name('creditOrderGoods')->insert([
                    'order_id'=>$result,
                    'goods_id'=>$goods['id'],
                    'goods_title'=>$goods['title'],
                    'goods_image'=>$goods['image'],
                    'goods_price'=>$goods['price'],
                    'count'=>$goods['count'],
                    'sort'=>$i++
                ]);
                //扣库存,加销量
                Db::name('Goods')->where('id',$goods['id'])
                    ->dec('storage',$goods['count'])
                    ->inc('sale',$goods['count'])
                    ->update();
            }
            $this->commit();
        }else{
            $this->error = "下单失败";
            $this->rollback();
        }
        if($status>0 ){
            self::getInstance()->updateStatus(['status'=>$status,'pay_time'=>time()],['order_id'=>$result]);
        }
        return $result;
    }


}