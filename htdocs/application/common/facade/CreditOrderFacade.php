<?php

namespace app\common\facade;

use extcore\SimpleFacade;

/**
 * Class CreditOrderFacade
 * @package app\common\facade
 * @see \app\common\model\CreditOrderModel
 * @method bool makeOrder($member,$products,$address,$paycredit,$remark,$balance_pay=1) static 下单
 * @method string getError() static 获取错误信息
 */
class CreditOrderFacade extends SimpleFacade
{
    protected static function getFacadeClass(){
        return \app\common\model\CreditOrderModel::class;
    }
}