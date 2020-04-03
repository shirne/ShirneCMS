<?php

namespace app\admin\validate;


use think\Validate;

/**
 * 帮助资料验证
 * Class HelpValidate
 * @package app\admin\validate
 */
class HelpValidate extends Validate
{
    protected $rule=array(
        'title'=>'require|max:100',
        'description'=>'max:250'
    );
    protected $message=array(
        'title.require'=>'请填写文章标题',
        'title.max'=>'文章标题不能超过100个字符',
        'description.max'=>'简介长度不能超过250'
    );

}