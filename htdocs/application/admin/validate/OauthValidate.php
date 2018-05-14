<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/5/14
 * Time: 13:02
 */

namespace app\admin\validate;


use think\Validate;

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