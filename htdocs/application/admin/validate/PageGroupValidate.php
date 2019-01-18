<?php

namespace app\admin\validate;


use app\common\validate\BaseUniqueValidate;

/**
 * 页面分组
 * Class PageGroupValidate
 * @package app\admin\validate
 */
class PageGroupValidate extends BaseUniqueValidate
{
    protected $rule=array(
        'group_name'=>'require',
        'group'=>'require|unique:pageGroup,%id%'
    );
    protected $message = array(
        'group_name.require'=>'请填写分组名称！',
        'group.require'=>'请填写分组标识！',
        'group.unique'=>'分组标识已经存在！'
    );
}