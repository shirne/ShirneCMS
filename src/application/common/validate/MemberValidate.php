<?php
/**
 * 会员资料验证
 * User: shirne
 * Date: 2018/4/29
 * Time: 10:34
 */

namespace app\common\validate;

use app\common\core\BaseUniqueValidate;

class MemberValidate extends BaseUniqueValidate
{
    protected $rule  = array(
        'username'=>['require', 'regex' => '/^[a-zA-Z][A-Za-z0-9\-\_]{5,19}$/','unique'=>'member,%id%'],
        'email'=>'email|unique:member,%id%',
        'mobile'=>'mobile|unique:member,%id%',
        'password'=>'require|min:6',
        'repassword'=>'require|confirm:password'
    );

    protected $message   = array(
        'username.require' => '请填写用户名',
        'username.unique' => '用户名已被占用',
        'username.regex'=>'用户名格式不正确',
        'email' => '邮箱格式错误',
        'email.unique' => '邮箱已被占用',
        'mobile' => '手机号码格式错误',
        'mobile.unique' => '手机号码已被占用',
        'password.require' => '请填写密码',
        'password.min' => '密码不能低于6位',
        'repassword.require' => '请确认密码',
        'repassword.confirm' => '两次密码输入不一致',
    );


    protected $scene = array(
        'register'=>['username','password','repassword','email','mobile'],
        'edit'=>['email','mobile'],
        'repassword'=>['password','repassword']
    );
}