<?php

namespace app\admin\validate;


use app\common\core\BaseUniqueValidate;

/**
 * 管理员资料验证
 * Class ManagerValidate
 * @package app\admin\validate
 */
class ManagerValidate extends BaseUniqueValidate
{
    protected $rule  = array(
        'username' => 'require|unique:manager,%id%',
        'email' => 'email|unique:manager,%id%',
        'mobile' => 'mobile|unique:manager,%id%',
        'password' => 'require|min:6',
        'type' => 'require|min:1'
    );

    protected $message   = array(
        'name.require' => '请填写用户名',
        'name.unique' => '用户名已存在',
        'email' => '邮箱格式错误',
        'email.unique' => '邮箱已存在',
        'mobile' => '手机号码格式错误',
        'mobile.unique' => '手机号码已存在',
        'password.require' => '手机号码格式错误',
        'password.min' => '手机号码已存在',
        'type' => '请设置管理员角色',
    );

    protected $scene = array(
        'edit' => ['name', 'email', 'mobile']
    );
}
