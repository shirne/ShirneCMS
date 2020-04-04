<?php
/**
 * 订单功能
 * User: shirne
 * Date: 2018/5/13
 * Time: 23:57
 */

namespace app\index\controller;


use app\common\facade\CreditOrderFacade;
use app\common\model\CreditOrderModel;
use app\common\validate\OrderValidate;
use think\facade\Db;

class CreditOrderController extends AuthedController
{
    public function initialize()
    {
        parent::initialize();
        $this->assign('navmodel','goods');
    }

    /**
     * 确认下单
     * @param $goods_ids string
     * @param $count int
     * @return \think\Response
     */
    public function confirm($goods_ids,$count=1)
    {
        $ordertype=1;

        $goodss=Db::name('Goods')
            ->whereIn('id',idArr($goods_ids))
            ->select();
        $counts=idArr($count);
        $this->initLevel();

        foreach ($goodss as $k=>&$item){
            $item['goods_price']=$item['price'];


            if(!empty($item['image']))$item['goods_image']=$item['image'];
            if(isset($counts[$k])){
                $item['count']=$counts[$k];
            }else{
                $item['count']=$counts[0];
            }
            if(!empty($item['levels'])){
                $levels=json_decode($item['levels'],true);
                if(!empty($levels)) {
                    if (!in_array($this->user['level_id'], $levels)) {
                        $this->error('您当前会员组不允许兑换商品[' . $item['goods_title'] . ']');
                    }
                }
            }
            if($item['type']==2){
                $ordertype=2;
            }
        }
        unset($item);


        $total_price=0;
        foreach ($goodss as $item){
            $total_price += $item['goods_price']*$item['count'];
        }

        if($this->request->isPost()){
            $data=$this->request->only(['address_id','remark','need_pay','pay_type','sec_password'],'post');
            $balancepay=$data['pay_type']=='balance'?1:0;
            if($balancepay){
                if(USE_SEC_PASSWORD && !compare_secpassword($this->user,$data['sec_password'])){
                    $this->error('安全密码验证错误');
                }
            }
            $validate=new OrderValidate();
            if(!$validate->check($data)){
                $this->error($validate->getError());
            }else{
                $address=Db::name('MemberAddress')->where('member_id',$this->userid)
                    ->where('address_id',$data['address_id'])->find();
                $pay_credit=$total_price;
                if($this->user['points']<$pay_credit*100){
                    $pay_credit = $this->user['points'] / 100;
                }

                $orderModel=new CreditOrderFacade();
                $result=$orderModel->makeOrder($this->user,$goodss,$address,$pay_credit,$data['remark'],$balancepay,$ordertype);
                if($result){
                    if($balancepay) {
                        $this->success('下单成功',url('index/member/order_detail',['id'=>$result]));
                    }else{
                        if(!in_array($data['pay_type'],['wechat'])){
                            $this->error('下单成功，支付方式错误！',url('index/member/credit_order_detail',['id'=>$result]));
                        }
                        $this->success('下单成功，即将跳转到支付页面',url('index/order/'.$data['pay_type'].'pay',['order_id'=>'PO_'.$result]));
                    }
                }else{
                    $this->error($orderModel->getError()?:'下单失败');
                }
            }
        }
        if(empty($goodss)){
            $this->error('产品不存在');
        }

        $addresses=Db::name('MemberAddress')->where('member_id',$this->userid)
            ->select();

        $this->assign('addresses',$addresses);
        $this->assign('total_price',$total_price);
        $this->assign('goodss',$goodss);
        return $this->fetch();
    }

}