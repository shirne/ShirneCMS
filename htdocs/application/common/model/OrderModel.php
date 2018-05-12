<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/5/12
 * Time: 8:26
 */

namespace app\common\model;


use think\Db;
use think\Model;

class OrderModel extends Model
{
    private function create_no(){
        $maxid=$this->field('max(order_id) as maxid')->find();
        $maxid = $maxid['maxid'];
        if(empty($maxid))$maxid=10000;
        return date('YmdHis'.str_pad($maxid+1,8,'0',STR_PAD_LEFT));
    }

    /**
     * @param $member
     * @param $products
     * @param $address
     * @param $content
     * @param $balance_pay
     * @return mixed
     */

    public function makeOrder($member,$products,$address,$content,$balance_pay=1){

        //status 0-待付款 1-已付款
        $this->startTrans();

        $status=0;
        $total_price=0;
        foreach ($products as $product){
            $total_price += intval($product['price']*100) * $product['count'];
        }

        //折扣
        $levels=getMemberLevels();
        $level=$levels[$member['level_id']];
        if(!empty($level) && $level['discount']<100){
            $total_price = $total_price*$level['discount']/100;
        }

        //todo  优惠券

        if($balance_pay) {
            $debit = money_log($member['id'], -$total_price, "下单支付", 'consume');
            if ($debit) $status = 1;
            else{
                $this->error="余额不足";
                return false;
            }
        }

        $orderdata=array(
            'order_no'=>$this->create_no(),
            'member_id'=>$member['id'],
            'level_id'=>$product['level_id'],
            'payamount'=>$total_price,
            'status'=>$status,
            'isaudit'=>getSetting('autoaudit')==1?1:0,
            'content'=>$content,
            'address_id'=>$address['address_id'],
            'recive_name'=>$address['recive_name'],
            'mobile'=>$address['mobile'],
            'province'=>$address['province'],
            'city' =>$address['city'],
            'area'=>$address['area'],
            'address' =>$address['address'],
            'create_at'=>time(),
            'pay_at'=>0,
            'express_no' =>'',
            'express_code'=>''
        );
        if($status>0){
            $orderdata['pay_at']=time();
        }
        $result= $this->insert($orderdata);

        if($result){
            $i=0;
            foreach ($products as $product){
                $product['order_id']=$result;
                Db::name('orderProduct')->insert([
                    'order_id'=>$result,
                    'product_id'=>$product['id'],
                    'product_title'=>$product['title'],
                    'product_image'=>$product['image'],
                    'product_price'=>$product['price'],
                    'count'=>$product['count'],
                    'sort'=>$i++
                ]);
            }
            //$this->_set_level($orderdata,$member);
            $this->commit();
            if($status>0 && $orderdata['isaudit']==1){
                $autotree=getSetting('autotree');
                if($autotree=='1') {
                    //$order = $this->where(array('apply_id' => $result))->find();
                    //$this->triggerTree($order);
                }
            }
        }else{
            $this->rollback();
        }
        return $result;
    }
}