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
    protected $pk='order_id';
    
    private function create_no(){
        $maxid=$this->field('max(order_id) as maxid')->find();
        $maxid = $maxid['maxid'];
        if(empty($maxid))$maxid=0;
        return date('YmdHis'.str_pad($maxid+1,8,'0',STR_PAD_LEFT));
    }

    public static function init()
    {
        parent::init();
        self::afterWrite(function ( $model)
        {
            $where=$model->getWhere();if(empty($where))return;
            $orders=$model->where($model->getWhere())->select();
            if(!empty($orders)) {
                foreach ($orders as $order) {
                    if ($order['status'] > 0 && $order['isaudit'] == 1) {
                        self::setLevel($order);
                        $rebated=self::doRebate($order);
                        if($rebated){
                            Db::name('Order')->where('order_id',$order['order_id'])
                                ->update(['rebated'=>1,'rebate_time'=>time()]);
                        }
                    }
                }
            }
        });
    }

    /**
     * @param $member
     * @param $products
     * @param $address
     * @param $remark
     * @param $balance_pay
     * @return mixed
     */

    public function makeOrder($member,$products,$address,$remark,$balance_pay=1,$ordertype=1){

        //折扣
        $levels=getMemberLevels();
        $level=$levels[$member['level_id']];
        $discount=1;
        if(!empty($level) && $level['discount']<100){
            $discount = $level['discount']*.01;
        }

        //status 0-待付款 1-已付款
        $status=0;
        $total_price=0;
        $commission_amount=0;
        foreach ($products as $k=>$product){
            if($product['storage']<$product['count']){
                $this->error='商品['.$product['product_title'].']库存不足';
                return false;
            }
            if($product['count']<1){
                $this->error='商品['.$product['product_title'].']数量错误';
                return false;
            }

            $price=intval($product['product_price']*100) * $product['count'];
            if($product['is_discount']){
                $price*=$discount;
            }
            $total_price += $price;

            if($product['is_commission'] ){
                $cost_price=intval($product['cost_price']*100)* $product['count'];
                if($price>$cost_price) {
                    $commission_amount += $price - $cost_price;
                }
            }
        }

        //todo  优惠券

        
        $this->startTrans();

        if($balance_pay) {
            $debit = money_log($member['id'], -$total_price, "下单支付", 'consume',is_string($balance_pay)?$balance_pay:'money');
            if ($debit) $status = 1;
            else{
                $this->error="余额不足";
                return false;
            }
        }
        $time=time();
        $orderdata=array(
            'order_no'=>$this->create_no(),
            'member_id'=>$member['id'],
            'level_id'=>0,
            'payamount'=>$total_price*.01,
            'commission_amount'=>$commission_amount*.01,
            'status'=>$status,
            'isaudit'=>getSetting('autoaudit')==1?1:0,
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
            'express_code'=>'',
            'type'=>$ordertype,
        );
        if($status>0){
            $orderdata['pay_time']=time();
        }
        $result= $this->insert($orderdata);

        if($result){
            $i=0;
            foreach ($products as $product){
                $product['order_id']=$result;
                $release_price=$product['product_price'];
                if($product['is_discount']){
                    $release_price *= $discount;
                }
                Db::name('orderProduct')->insert([
                    'order_id'=>$result,
                    'product_id'=>$product['product_id'],
                    'sku_id'=>$product['sku_id'],
                    'product_title'=>$product['product_title'],
                    'product_image'=>$product['product_image'],
                    'product_orig_price'=>$product['product_price'],
                    'product_price'=>$release_price,
                    'count'=>$product['count'],
                    'sort'=>$i++
                ]);
                //扣库存,加销量
                Db::name('ProductSku')->where('sku_id',$product['sku_id'])
                    ->dec('storage',-$product['count'])
                    ->inc('sale',$product['count'])
                    ->update();
                Db::name('Product')->where('id',$product['product_id'])
                    ->dec('storage',-$product['count'])
                    ->inc('sale',$product['count'])
                    ->update();
            }
            $this->commit();
        }else{
            $this->error = "入单失败";
            $this->rollback();
        }
        return $result;
    }

    /**
     * 根据设置或升级原则进行升级
     */
    public static function setLevel($order){
        
    }

    public static function doRebate($order){
        if($order['rebated'])return false;
        $member=Db::name('Member')->find($order['member_id']);
        if(empty($member))return true;
        $levels=getMemberLevels();
        $levelConfig=getLevelConfig($levels);
        $parents=getMemberParents($member['id'],$levelConfig['commission_layer'],false);
        if(empty($parents))return true;

        $pids=array_column($parents,'id');
        Db::name('Member')->whereIn('id', $pids)->setInc('week_performance', $order['payamount'] * 100);
        Db::name('Member')->whereIn('id', $pids)->setInc('total_performance', $order['payamount'] * 100);

        for ($i = 0; $i < count($parents); $i++) {
            $curLevel=$levels[$parents[$i]['level_id']];
            if($curLevel['commission_layer']>$i && !empty($levels['commission_percent'][$i])) {
                $curPercent = $levels['commission_percent'][$i];
                $amount=$order['commission_amount']*$curPercent;
                self::award_log($parents[$i]['id'],$order['member_id'],$amount,'消费分佣'.($i+1).'代','commission');
            }
        }
        return true;
    }
    public static function award_log($uid,$from_uid, $money, $reson, $type)
    {
        $amount=$money*100;
        money_log([$uid,$from_uid], $amount, $reson, $type,'credit');

        //返奖同时可以处理其它

    }
}