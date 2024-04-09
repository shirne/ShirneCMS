<?php

namespace app\admin\validate;


use app\common\core\BaseUniqueValidate;

/**
 * 帮助分类验证
 * Class HelpCategoryValidate
 * @package app\admin\validate
 */
class HelpCategoryValidate extends BaseUniqueValidate
{
    protected $rule = array(
        'title' => 'require',
        'short' => 'max:20',
        'name' => 'require|unique:helpCategory,%id%'
    );
    protected $message = array(
        'title.require' => '请填写分类标题',
        'name.require' => '请填写分类别名',
        'name.unique' => '分类别名已存在',
        'short.max' => '简称长度不能超过20'
    );
}
