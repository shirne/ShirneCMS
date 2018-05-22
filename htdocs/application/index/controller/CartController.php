<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/5/14
 * Time: 9:35
 */

namespace app\index\controller;


use app\common\facade\MemberCartFacade;
use think\Db;

class CartController extends AuthedController
{
    public function index(){
        $carts=MemberCartFacade::getCart($this->userid);
        $this->assign('carts',$carts);
        return $this->fetch();
    }

    public function add($sku_id,$count=1){
        $sku=Db::name('ProductSku')->where('sku_id',$sku_id)->find();
        if(empty($sku)){
            $this->error('产品已下架');
        }
        $product=Db::name('Product')->where('id',$sku['product_id'])->find();
        if(empty($product) || $product['status']==0){
            $this->error('产品已下架');
        }
        $added=MemberCartFacade::addCart($product,$sku,$count,$this->userid);
        if($added){
            $this->success('添加成功');
        }else{
            $this->error('添加失败');
        }

    }
    public function update($sku_id,$count){
        $result=MemberCartFacade::updateCart($sku_id,$count,$this->userid);
        if($result){
            $this->success('更新成功');
        }else{
            $this->error('更新失败');
        }
    }
    public function del($sku_id){
        $result=MemberCartFacade::delCart($sku_id,$this->userid);
        if($result){
            $this->success('移除成功');
        }else{
            $this->error('移除失败');
        }
    }

    public function clear(){
        $result=MemberCartFacade::clearCart($this->userid);
        if($result){
            $this->success('清除成功');
        }else{
            $this->error('清除失败');
        }
    }
}