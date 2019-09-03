<?php


namespace app\admin\validate;


use think\Validate;

class PostageValidate extends Validate
{
    protected $rule=array(
        'title'=>'require'
    );
    
    protected $message=array(
        'title.require'=>'请填写模板名称'
    );
}