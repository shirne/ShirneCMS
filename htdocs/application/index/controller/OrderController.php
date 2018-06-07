<?php
/**
 * 订单功能
 * User: shirne
 * Date: 2018/5/13
 * Time: 23:57
 */

namespace app\index\controller;


use app\common\facade\MemberCartFacade;
use app\common\facade\OrderFacade;
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
    public function confirm($sku_ids,$count=1,$from='quick')
    {

        if($from=='cart'){
            $products=MemberCartFacade::getCart($this->userid,$sku_ids);
        }else{
            $products=Db::view('ProductSku','*')
                ->view('Product',['title'=>'product_title','image'=>'product_image','levels','is_discount'],'ProductSku.product_id=Product.id','LEFT')
                ->whereIn('ProductSku.sku_id',idArr($sku_ids))
                ->select();
            $counts=idArr($count);
            $this->initLevel();
            foreach ($products as $k=>&$item){
                $item['product_price']=$item['price'];

                if($item['is_discount'] && $this->userLevel['discount']){
                    $item['product_price']=$item['product_price']*$this->userLevel['discount']*.01;
                }
                if(!empty($item['image']))$item['product_image']=$item['image'];
                if(isset($counts[$k])){
                    $item['count']=$counts[$k];
                }else{
                    $item['count']=$counts[0];
                }
                if(!empty($item['levels'])){
                    $levels=json_decode($item['levels'],true);
                    if(!in_array($this->user['level_id'],$levels)){
                        $this->error('您当前会员组不允许购买商品['.$item['product_title'].']');
                    }
                }
            }
            unset($item);
        }

        $total_price=0;
        foreach ($products as $item){
            $total_price += $item['product_price']*$item['count'];
        }

        if($this->request->isPost()){
            $data=$this->request->only('address_id,remark','post');
            $validate=new OrderValidate();
            if(!$validate->check($data)){
                $this->error($validate->getError());
            }else{
                $address=Db::name('MemberAddress')->where('member_id',$this->userid)
                    ->where('address_id',$data['address_id'])->find();
                $result=OrderFacade::makeOrder($this->user,$products,$address,$data['remark']);
                if($result){
                    $this->success('下单成功');
                }else{
                    $this->error('下单失败');
                }
            }
        }
        if(empty($products)){
            $this->error('产品不存在');
        }

        $address=Db::name('MemberAddress')->where('member_id',$this->userid)
            ->select();
        $this->assign('from',$from);
        $this->assign('address',$address);
        $this->assign('total_price',$total_price);
        $this->assign('products',$products);
        return $this->fetch();
    }
}