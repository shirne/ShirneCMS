<?php

namespace app\admin\validate;


use app\common\core\BaseUniqueValidate;

/**
 * 单页数据验证
 * Class PageValidate
 * @package app\admin\validate
 */
class PageValidate extends BaseUniqueValidate
{

    protected $rule=array(
        'title'=>'require',
        'name'=>'require|unique:page,%id%'
    );
    protected $message = array(
        'title.require'=>'请填写单页标题！',
        'name.require'=>'请填写单页别名！',
        'name.unique'=>'单页别名已经存在！'
    );
}