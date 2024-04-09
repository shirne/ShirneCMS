<?php

namespace app\admin\validate;


use think\Validate;

/**
 * 通用图片资料验证
 * Class ImagesValidate
 * @package app\admin\validate
 */
class ImagesValidate extends Validate
{
    protected $rule = array(
        'title|图片名称' => 'require'
    );
}
