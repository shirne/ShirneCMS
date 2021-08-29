<?php

namespace app\common\facade;

use app\common\core\SimpleFacade;

/**
 * Class CreditOrderFacade
 * @package app\common\facade
 * @see \app\common\model\CreditOrderModel
 * @method static bool makeOrder($member,$products,$address,$paycredit,$remark,$balance_pay=1) 下单
 * @method static string getError() 获取错误信息
 */
class CreditOrderFacade extends SimpleFacade
{
    protected static function getFacadeClass(){
        return \app\common\model\CreditOrderModel::class;
    }
}