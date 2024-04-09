<?php

namespace app\admin\validate;


use app\common\core\BaseUniqueValidate;

/**
 * 文章分类验证
 * Class CategoryValidate
 * @package app\admin\validate
 */
class CategoryValidate extends BaseUniqueValidate
{
    protected $rule = array(
        'title' => 'require',
        'short' => 'max:20',
        'name' => ['require', 'unique' => 'category,%id%', 'regex' => '/^[a-zA-Z]\w+$/']
    );
    protected $message = array(
        'title.require' => '请填写分类标题',
        'name.require' => '请填写分类别名',
        'name.regex' => '分类别名用于生成url, 请填写字母+数字格式',
        'name.unique' => '分类别名已存在',
        'short.max' => '简称长度不能超过20'
    );
}
