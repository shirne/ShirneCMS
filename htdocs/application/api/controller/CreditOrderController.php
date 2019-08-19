<?php

namespace app\api\Controller;

use app\admin\model\MemberLevelModel;
use app\common\facade\CreditOrderFacade;
use app\common\model\MemberOauthModel;
use app\common\model\CreditOrderModel;
use app\common\model\WechatModel;
use app\common\validate\OrderValidate;
use EasyWeChat\Factory;
use think\Db;


/**
 * 订单操作
 * Class OrderController
 * @package app\api\Controller
 */
class CreditOrderController extends AuthedController
{
    public function prepare(){
        $order_skus=$this->input['goods'];
        $skuids=array_column($order_skus,'sku_id');
        $goods=Db::view('GoodsSku','*')
            ->view('Goods',['title'=>'product_title','image'=>'product_image','levels','is_discount'],'GoodsSku.product_id=Goods.id','LEFT')
            ->whereIn('GoodsSku.sku_id',idArr($skuids))
            ->select();

        return $this->response([
            'goods'=>$goods,
            'address'=>Db::name('MemberAddress')->where('member_id',$this->user['id'])->order('is_default DESC')->find(),
            'express'=>[
                'fee'=>0,
                'title'=>'快递免邮'
            ]
        ]);

    }
    public function confirm(){
        $input_goods=$this->input['goods'];
        if(empty($input_goods))$this->error('未选择下单商品');
        $goods_ids = array_column($input_goods,'id');
        $goods=Db::view('Goods','*')
            ->whereIn('Goods.id',idArr($goods_ids))
            ->select();
        $counts=array_index($input_goods,'id,count');
        $userLevel=MemberLevelModel::get($this->user['level_id']);
        foreach ($goods as $k=>&$item){
            $item['price']=$item['price'];

            if(!empty($item['image']))$item['image']=$item['image'];
            if(isset($counts[$item['id']])){
                $item['count']=$counts[$item['id']];
            }else{
                $item['count']=1;
            }
            if(!empty($item['levels'])){
                $levels=json_decode($item['levels'],true);
                if(!in_array($this->user['level_id'],$levels)){
                    $this->error('您当前会员组不允许购买商品['.$item['title'].']');
                }
            }
        }
        unset($item);
        

        $total_price=0;
        foreach ($goods as $item){
            $total_price += $item['price']*$item['count'];
            
        }
        //todo 邮费模板

        if($total_price != $this->input['total_price']){
            $this->error('下单商品价格已变动');
        }

        $data=$this->request->only('address_id,pay_type,remark,form_id','put');

        $validate=new OrderValidate();
        if(!$validate->check($data)){
            $this->error($validate->getError());
        }else{
            $address=Db::name('MemberAddress')->where('member_id',$this->user['id'])
                ->where('address_id',$data['address_id'])->find();
            $balancepay=$data['pay_type']=='balance'?1:0;

            $result=CreditOrderFacade::makeOrder($this->user,$goods,$address,$data['remark'],$balancepay);
            if($result){
                if($balancepay) {
                    return $this->response(['order_id',$result],1,'下单成功');
                }else{
                    $method=$data['pay_type'].'pay';
                    if(method_exists($this,$method)){
                        return $this->$method($result);
                    }else{
                        return $this->response(['order_id',$result],1,'下单成功，请尽快支付');
                    }

                }
            }else{
                $this->error('下单失败');
            }
        }
    }

}