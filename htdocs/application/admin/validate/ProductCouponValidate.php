<?php

namespace app\admin\validate;

use think\Validate;


/**
 * 优惠券数据验证
 * Class ProductCouponValidate
 * @package app\admin\validate
 */
class ProductCouponValidate extends Validate
{
    protected $rule=array(
        'title|优惠券名称'=>'require'
    );
}