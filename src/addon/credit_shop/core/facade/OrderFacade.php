<?php

namespace addon\credit_shop\core\facade;

use app\common\core\SimpleFacade;

/**
 * Class OrderFacade
 * @package app\common\facade
 * @see \addon\credit_shop\core\model\CreditOrderModel
 * @method static bool makeOrder($member,$products,$address,$paycredit,$remark,$balance_pay=1) 下单
 * @method static string getError() 获取错误信息
 */
class OrderFacade extends SimpleFacade
{
    protected static function getFacadeClass(){
        return \addon\credit_shop\core\model\CreditOrderModel::class;
    }
}