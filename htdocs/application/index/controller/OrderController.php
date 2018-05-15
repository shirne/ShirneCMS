<?php
/**
 * 订单功能
 * User: shirne
 * Date: 2018/5/13
 * Time: 23:57
 */

namespace app\index\controller;


use app\common\facade\MemberCartModel;
use app\common\facade\OrderModel;
use app\common\validate\OrderValidate;
use think\Db;

class OrderController extends AuthedController
{

    /**
     * 确认下单
     * @param $sku_ids string
     * @param $count int
     * @param  $from string
     * @return \think\Response
     */
    public function confirm($sku_ids,$count=1,$from='cart')
    {

        if($from=='cart'){
            $products=MemberCartModel::getCart($this->userid,$sku_ids);
        }else{
            $products=Db::view('ProductSku','*')
                ->view('Product',['title'=>'product_title'],'ProductSku.product_id=Product.id','LEFT')
                ->select();
            $counts=idArr($count);
            $products->each(function($item,$key) use ($counts){
                if(isset($counts[$key])){
                    $item['count']=$counts[$key];
                }else{
                    $item['count']=$counts[0];
                }
                return $item;
            });
        }

        if($this->request->isPost()){
            $data=$this->request->only('address_id','post');
            $validate=new OrderValidate();
            if(!$validate->check($data)){
                $this->error($validate->getError());
            }else{
                $address=Db::name('MemberAddress')->where('member_id',$this->userid)
                    ->where('address_id',$data['address_id'])->find();
                OrderModel::makeOrder($this->user,$products,$address,$data['remark']);
            }
        }

        $address=Db::name('MemberAddress')->where('member_id',$this->userid)
            ->where('is_default',1)->find();
        $this->assign('from',$from);
        $this->assign('address',$address);
        $this->assign('products',$products);
        return $this->fetch();
    }
}