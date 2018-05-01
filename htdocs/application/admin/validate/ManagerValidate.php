<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/4/29
 * Time: 10:34
 */

namespace app\admin\validate;


use app\common\validate\BaseUniqueValidate;

class ManagerValidate extends BaseUniqueValidate
{
    protected $rule  = array(
        'username'=>'require|unique:manager,%id%',
        'email'=>'email|unique:manager,%id%',
        'mobile'=>'mobile|unique:manager,%id%',
        'password'=>'require|min:6'
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
    );

    protected $scene = array(
        'edit'=>['name','email','mobile']
    );

}