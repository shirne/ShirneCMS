<?php

namespace addon\credit_shop\core\facade;

use app\common\core\SimpleFacade;

/**
 * Class OrderFacade
 * @package app\common\facade
 * @see \addon\credit_shop\core\model\CreditOrderModel
 * @method bool makeOrder($member,$products,$address,$paycredit,$remark,$balance_pay=1) static 下单
 * @method string getError() static 获取错误信息
 */
class OrderFacade extends SimpleFacade
{
    protected static function getFacadeClass(){
        return \addon\credit_shop\core\model\CreditOrderModel::class;
    }
}