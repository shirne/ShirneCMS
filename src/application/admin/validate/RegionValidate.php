<?php

namespace app\admin\validate;


use app\common\core\BaseUniqueValidate;

/**
 * 地区验证
 * Class CategoryValidate
 * @package app\admin\validate
 */
class RegionValidate extends BaseUniqueValidate
{
    protected $rule=array(
        'title'=>'require',
        'short'=>'max:20',
        'name'=>'require|unique:region,%id%'
    );
    protected $message=array(
        'title.require'=>'请填写地区名称',
        'name.require'=>'请填写地区拼音',
        'name.unique'=>'地区拼音不可重复',
        'short.max'=>'地区简称长度不能超过20'
    );
}