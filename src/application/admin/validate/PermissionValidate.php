<?php

namespace app\admin\validate;


use app\common\core\BaseUniqueValidate;

/**
 * 菜单数据验证
 * Class PermissionValidate
 * @package app\admin\validate
 */
class PermissionValidate extends BaseUniqueValidate
{
    protected $rule = array(
        'name' => 'require',
        'key' => 'require|unique:permission,%id%'
    );

    protected $message = array(
        'name.require' => '请填写菜单名称',
        'key.require' => '请填写菜单键名',
        'key.unique' => '键名已存在'
    );
}
