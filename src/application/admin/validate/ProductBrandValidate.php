<?php

namespace app\admin\validate;

use think\Validate;

/**
 * 品牌数据验证
 * Class ProductBrandValidate
 * @package app\admin\validate
 */
class ProductBrandValidate extends Validate
{
    protected $rule = array(
        'title' => 'require',
        'url' => 'url'
    );
    protected $message = array(
        'title' => '请填写品牌名称',
        'url' => '官网地址格式错误',
    );
}
