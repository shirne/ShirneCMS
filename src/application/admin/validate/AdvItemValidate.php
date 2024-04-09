<?php

namespace app\admin\validate;


use think\Validate;

/**
 * Class AdvItemValidate
 * @package app\admin\validate
 */
class AdvItemValidate extends Validate
{
    protected $rule = array(
        'title|广告名称' => 'require'
    );
}
