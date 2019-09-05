<?php

namespace app\api\controller;

use app\common\facade\MemberCartFacade;
use think\Db;

/**
 * 购物车操作接口
 * Class CartController
 * @package app\api\Controller
 */
class CartController extends AuthedController
{
    public function getall(){
        $list = MemberCartFacade::getCart($this->user['id']);
        $list = empty2null($list,'spec_data,specs');
        return $this->response($list);
    }

    public function getcount(){
        return $this->response(MemberCartFacade::getCount($this->user['id']));
    }

    public function add($sku_id,$count=1){
        $sku=Db::name('ProductSku')->where('sku_id',$sku_id)->find();
        if(empty($sku)){
            $this->error('型号不存在');
        }
        if($sku['storage']<1){
            $this->error('型号库存不足');
        }
        $product=Db::name('Product')->where('id',$sku['product_id'])
            ->where('status',1)->find();
        if(empty($product)){
            $this->error('产品已下架');
        }
        MemberCartFacade::addCart($product,$sku,$count,$this->user['id']);
        $this->success('成功添加到购物车');
    }

    public function update($sku_id,$count=1,$id=0){
        $sku=Db::name('ProductSku')->where('sku_id',$sku_id)->find();
        if(empty($sku)){
            $this->error('型号不存在');
        }
        $product=Db::name('Product')->where('id',$sku['product_id'])
            ->where('status',1)->find();
        if(empty($product)){
            $this->error('产品已下架');
        }
        if($count>$sku['storage']){
            $count=$sku['storage'];
        }
        if($id>0){
            MemberCartFacade::updateCartData($product,$sku,$this->user['id'],$id);
        }else {
            MemberCartFacade::updateCart($sku_id, $count, $this->user['id']);
        }
        $this->success('购物车已更新');
    }

    public function delete($sku_id){
        MemberCartFacade::delCart($sku_id,$this->user['id']);
        $this->success('购物车已更新');
    }

    public function clear(){
        MemberCartFacade::clearCart($this->user['id']);
        $this->success('购物车已清空');
    }
}