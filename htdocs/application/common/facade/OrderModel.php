<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/5/12
 * Time: 8:28
 */

namespace app\common\facade;


use think\Facade;

class OrderModel extends Facade
{
    protected static function getFacadeClass(){
        return \app\common\model\OrderModel::class;
    }
}