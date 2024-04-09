<?php

namespace app\index\controller;


use app\common\facade\MemberCartFacade;
use think\Db;

/**
 * Class CartController
 * @package app\index\controller
 */
class CartController extends AuthedController
{
    public function initialize()
    {
        parent::initialize();
        $this->assign('navmodel', 'product');
    }

    public function index()
    {
        $carts = MemberCartFacade::getCart($this->userid);
        $this->assign('carts', $carts);
        return $this->fetch();
    }

    public function add($sku_id, $count = 1)
    {
        $sku = Db::name('ProductSku')->where('sku_id', $sku_id)->find();
        if (empty($sku)) {
            $this->error('产品已下架');
        }
        $product = Db::name('Product')->where('id', $sku['product_id'])->find();
        if (empty($product) || $product['status'] == 0) {
            $this->error('产品已下架');
        }
        if (!empty($product['levels'])) {
            $levels = @json_decode($product['levels'], true);
            if (!empty($levels) && !in_array($this->user['level_id'], $levels)) {
                $this->error('您当前会员组不允许购买商品[' . $product['title'] . ']');
            }
        }
        $added = MemberCartFacade::addCart($product, $sku, $count, $this->userid);
        if ($added) {
            $this->success('添加成功');
        } else {
            $this->error('添加失败');
        }
    }
    public function update($sku_id, $count)
    {
        $result = MemberCartFacade::updateCart($sku_id, $count, $this->userid);
        if ($result) {
            $this->success('更新成功');
        } else {
            $this->error('更新失败');
        }
    }
    public function del($sku_id)
    {
        $result = MemberCartFacade::delCart($sku_id, $this->userid);
        if ($result) {
            $this->success('移除成功');
        } else {
            $this->error('移除失败');
        }
    }

    public function clear()
    {
        $result = MemberCartFacade::clearCart($this->userid);
        if ($result) {
            $this->success('清除成功');
        } else {
            $this->error('清除失败');
        }
    }
}
