<?php

namespace app\common\model;


use think\Db;
use third\KdExpress;

class CreditOrderModel extends BaseModel
{
    protected $pk='order_id';
    protected $type = [];

    private function create_no(){
        $maxid=$this->field('max(order_id) as maxid')->find();
        $maxid = $maxid['maxid'];
        if(empty($maxid))$maxid=0;
        return date('YmdHis').$this->pad_orderid($maxid+1,3);
    }
    private function pad_orderid($id,$len=3){
        $strlen=strlen($id);
        return $strlen<$len?str_pad($id,$len,'0',STR_PAD_LEFT):substr($id,$strlen-$len);
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
                        
                        
                    }elseif($order['status']<0 && $order['cancel_time']==0){
                        $goodss=Db::name('creditOrderGoods')->where('order_id',$order['order_id'])->select();
                        foreach ($goodss as $goods) {
                            Db::name('Goods')->where('id', $goods['goods_id'])
                                ->inc('storage', $goods['count'])
                                ->dec('sale', $goods['count'])
                                ->update();
                        }
                        Db::name('Order')->where('order_id',$order['order_id'])
                            ->update(['cancel_time'=>time()]);
                    }
                }
            }
        });
    }

    /**
     * @param $member
     * @param $goodss
     * @param $address
     * @param $paycredit
     * @param $remark
     * @param $balance_pay
     * @param $ordertype
     * @return mixed
     */

    public function makeOrder($member,$goodss,$address,$paycredit,$remark,$balance_pay=1,$ordertype=1){

        //status 0-待付款 1-已付款
        $status=0;
        $total_price=0;
        foreach ($goodss as $k=>$goods){
            if($goods['storage']<$goods['count']){
                $this->error='商品['.$goods['goods_title'].']库存不足';
                return false;
            }
            if($goods['count']<1){
                $this->error='商品['.$goods['goods_title'].']数量错误';
                return false;
            }

            $price=intval($goods['goods_price']*100) * $goods['count'];

            $total_price += $price;

        }


        $this->startTrans();
        if($paycredit){
            $paycredit = $paycredit * 100;
            $decpoints = money_log($member['id'], -$paycredit, "积分支付", 'consume',0,'points');
            if(!$decpoints){
                $this->error="积分扣除失败";
                return false;
            }
        }

        $pay_price = $total_price - $paycredit;

        if($pay_price>0) {
            if ($balance_pay) {
                $debit = money_log($member['id'], -$pay_price, "积分商品抵扣", 'consume', 0, is_string($balance_pay) ? $balance_pay : 'money');
                if ($debit) $status = 1;
                else {
                    $this->error = "余额不足";
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
        $servModel=new MemberServiceModel();
        $services = $servModel->getServices($address);
        $orderdata = array_merge($orderdata,$services);
        /*if($status>0){
            $orderdata['pay_time']=time();
        }*/

        $result= $this->insert($orderdata,false,true);

        if($result){
            $i=0;
            foreach ($goodss as $goods){
                $goods['order_id']=$result;

                Db::name('creditOrderGoods')->insert([
                    'order_id'=>$result,
                    'goods_id'=>$goods['goods_id'],
                    'goods_title'=>$goods['goods_title'],
                    'goods_image'=>$goods['goods_image'],
                    'goods_price'=>$goods['goods_price'],
                    'count'=>$goods['count'],
                    'sort'=>$i++
                ]);
                //扣库存,加销量
                Db::name('Goods')->where('id',$goods['goods_id'])
                    ->dec('storage',$goods['count'])
                    ->inc('sale',$goods['count'])
                    ->update();
            }
            $this->commit();
        }else{
            $this->error = "入单失败";
            $this->rollback();
        }
        if($status>0 ){
            self::update(['status'=>$status,'pay_time'=>time()],['order_id'=>$result]);
        }
        return $result;
    }


    /**
     * @param bool $force
     * @return array
     */
    public function fetchExpress($force=false)
    {
        if($force || $this->express_time<time()-3600)
        {
            if(!$force && !empty($this->express_data)){
                $express=json_decode($this->express_data);
                if($express['Success']==true && $express['State']==3){
                    return $express;
                }
            }
            if(!empty($this->express_no) && !empty($this->express_code)) {
                $express = new KdExpress([
                    'appid'=>getSetting('kd_userid'),
                    'appsecret'=>getSetting('kd_apikey')
                ]);
                $data = $express->QueryExpressTraces($this->express_code, $this->express_no);
                $this->express_data=$data;
                $this->express_time=time();
                $this->save();
            }
        }

        return empty($this->express_data)?[]:json_decode($this->express_data,TRUE);
    }
}