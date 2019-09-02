<?php

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