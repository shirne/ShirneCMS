<?php

namespace app\common\facade;

use app\common\core\SimpleFacade;

/**
 * Class OrderFacade
 * @package app\common\facade
 * @see \app\common\model\OrderModel
 * @method static array getCounts($member_id=0)
 * @method static bool makeOrder($member,$products,$address,$remark,$balance_pay=1,$ordertype=1) 下单
 * @method static string getError() 获取错误信息
 */
class OrderFacade extends SimpleFacade
{
    protected static function getFacadeClass(){
        return \app\common\model\OrderModel::class;
    }
}