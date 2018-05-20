<?php

namespace app\common\facade;

use extcore\SimpleFacade;

/**
 * Class OrderFacade
 * @package app\common\facade
 * @see \app\common\model\OrderModel
 * @method bool makeOrder($member,$products,$address,$remark,$balance_pay=1) static 下单
 */
class OrderFacade extends SimpleFacade
{
    protected static function getFacadeClass(){
        return \app\common\model\OrderModel::class;
    }
}