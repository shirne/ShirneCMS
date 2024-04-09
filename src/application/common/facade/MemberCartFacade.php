<?php

namespace app\common\facade;

use app\common\core\SimpleFacade;

/**
 * Class MemberCartFacade
 * @package app\common\facade
 * @see \app\common\model\MemberCartModel
 * @method static array mapCart($product,$sku) 将产品数据转换为购物车数据
 * @method static array mapProduct($product,$sku) 将产品数据转换可下单数据
 * @method static int getCount($member_id) 获取商品数目
 * @method static bool addCart($product,$sku,$count,$member_id) 添加购物车
 * @method static bool updateCartData($product,$sku,$member_id,$id) 更新购物车资料
 * @method static bool updateCart($sku_id,$count,$member_id) 更新购物车
 * @method static array getCart($member_id,$sku_ids='') 获取购物车
 * @method static bool delCart($sku_ids,$member_id) 删除购物车
 * @method static bool clearCart($member_id) 清空购物车
 */
class MemberCartFacade extends SimpleFacade
{
    protected static function getFacadeClass()
    {
        return \app\common\model\MemberCartModel::class;
    }
}
