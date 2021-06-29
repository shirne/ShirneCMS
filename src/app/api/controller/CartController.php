<?php

namespace app\api\controller;

use app\common\facade\MemberCartFacade;
use think\facade\Db;

/**
 * 购物车操作接口
 * Class CartController
 * @package app\api\Controller
 */
class CartController extends AuthedController
{
    /**
     * 获取购物车全部列表
     * @return Json 
     */
    public function getall(){
        $list = MemberCartFacade::getCart($this->user['id']);
        $list = empty2null($list,'spec_data,specs');
        return $this->response($list);
    }

    /**
     * 获取购物车内商品数量
     * @return Json 
     */
    public function getcount(){
        return $this->response(MemberCartFacade::getCount($this->user['id']));
    }

    /**
     * 添加到购物车
     * @param mixed $sku_id 
     * @param int $count 
     * @return void 
     */
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
        if(!empty($product['levels'])){
            $levels=@json_decode($product['levels'],true);
            if (!empty($levels) && !in_array($this->user['level_id'], $levels)) {
                $this->error('您当前会员组不允许购买商品[' . $product['title'] . ']');
            }
        }
        MemberCartFacade::addCart($product,$sku,$count,$this->user['id']);
        $this->success('成功添加到购物车');
    }

    /**
     * 更新购物车
     * @param mixed $sku_id 
     * @param int $count 
     * @param int $id 
     * @return void 
     */
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

    /**
     * 删除购物车内指定的商品
     * @param mixed $sku_id 
     * @return void 
     */
    public function delete($sku_id){
        MemberCartFacade::delCart($sku_id,$this->user['id']);
        $this->success('购物车已更新');
    }

    /**
     * 清空购物车
     * @return void 
     */
    public function clear(){
        MemberCartFacade::clearCart($this->user['id']);
        $this->success('购物车已清空');
    }
}