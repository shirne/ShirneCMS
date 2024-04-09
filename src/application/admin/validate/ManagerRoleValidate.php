<?php

namespace app\admin\validate;


use app\common\core\BaseUniqueValidate;

/**
 * 角色资料验证
 * Class ManagerRoleValidate
 * @package app\admin\validate
 */
class ManagerRoleValidate extends BaseUniqueValidate
{
    protected $rule  = array(
        'role_name' => 'require|unique:managerRole,%id%',
        'type' => 'require|unique:managerRole,%id%'
    );

    protected $message   = array(
        'role_name.require' => '请填写角色名',
        'role_name.unique' => '角色名已存在',
        'type.require' => '请填写角色级别',
        'type.unique' => '级别已存在',
    );
}
