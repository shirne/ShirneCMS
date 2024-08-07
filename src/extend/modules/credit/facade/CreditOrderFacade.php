<?php

namespace modules\credit\facade;

use app\common\core\SimpleFacade;

/**
 * Class CreditOrderFacade
 * @package modules\credit\facade
 * @see \modules\credit\model\CreditOrderModel
 * @method static bool makeOrder($member,$products,$address,$paycredit,$remark,$balance_pay=1) 下单
 * @method static string getError() 获取错误信息
 */
class CreditOrderFacade extends SimpleFacade
{
    protected static function getFacadeClass()
    {
        return \modules\credit\model\CreditOrderModel::class;
    }
}
