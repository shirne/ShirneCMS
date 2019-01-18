<?php

namespace app\admin\validate;


use think\Validate;

/**
 * 第三方登录资料验证
 * Class OauthValidate
 * @package app\admin\validate
 */
class OauthValidate extends Validate
{
    protected $rule=array(
        'title'=>'require',
        'type'=>'require',
        'appid'=>'require',
        'appkey'=>'require'
    );
    protected $message=array(
        'title'=>'请填写名称',
        'type.require'=>'请选择类型',
        'appid.require'=>'请填写appid',
        'appkey.require'=>'请填写appkey',
    );
}