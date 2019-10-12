<?php

namespace app\admin\validate;


use app\common\core\BaseUniqueValidate;

/**
 * 规格数据验证
 * Class SpecificationsValidate
 * @package app\admin\validate
 */
class SpecificationsValidate extends BaseUniqueValidate
{
    protected $rule=array(
        'title'=>'require|unique:specifications,%id%',
        'data'=>'require'
    );
    protected $message=array(
        'title.require'=>'请填写规格名',
        'title.unique'=>'规格名已经存在',
        'data.require'=>'请填写规格值'
    );
}