<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/4/29
 * Time: 10:34
 */

namespace app\index\validate;


use think\Validate;

class ManagerValidate extends Validate
{
    public function setId($id=0){
        foreach ($this->rule as $f=>$rule){
            $this->rule[$f]=str_replace($rule,'%id%',$id);
        }
    }
    protected $rule  = array(
        'username'=>'require|unique:manager,%id%,manager_id',
        'email'=>'email|unique:manager,%id%,manager_id',
        'mobile'=>'mobile|unique:manager,%id%,manager_id',
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

}