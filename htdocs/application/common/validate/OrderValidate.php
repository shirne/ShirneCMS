<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/5/12
 * Time: 8:27
 */

namespace app\common\validate;


use think\Validate;

class OrderValidate extends Validate
{
    protected $rule=[
        'address_id'=>'require'
    ];
    protected $message=[
        'address_id.require'=>'请选择收货地址'
    ];
}