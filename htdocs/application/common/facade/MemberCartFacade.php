<?php

namespace app\common\facade;

use extcore\SimpleFacade;

/**
 * Class MemberCartFacade
 * @package app\common\facade
 * @see \app\common\model\MemberCartModel
 * @method array mapCart($product,$sku) static 将产品数据转换为购物车数据
 * @method array mapProduct($product,$sku) static 将产品数据转换可下单数据
 * @method bool addCart($product,$sku_id,$count,$member_id) static 添加购物车
 * @method bool updateCartData($product,$sku,$member_id) static 更新购物车资料
 * @method bool updateCart($sku_id,$count,$member_id) static 更新购物车
 * @method array getCart($member_id,$sku_ids='') static 获取购物车
 * @method bool delCart($sku_ids,$member_id) static 删除购物车
 * @method bool clearCart($member_id) static 清空购物车
 */
class MemberCartFacade extends SimpleFacade
{
    protected static function getFacadeClass(){
        return \app\common\model\MemberCartModel::class;
    }
}