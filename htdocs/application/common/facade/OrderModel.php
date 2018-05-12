<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/5/12
 * Time: 8:28
 */

namespace app\common\facade;


use think\Facade;

/**
 * Class OrderModel
 * @package app\common\facade
 * @see \app\common\model\OrderModel
 * @method bool makeOrder($member,$products,$address,$content,$balance_pay=1) static 下单
 */
class OrderModel extends Facade
{
    protected static function getFacadeClass(){
        return \app\common\model\OrderModel::class;
    }
}