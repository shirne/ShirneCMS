<?php

namespace app\admin\validate;


use think\Validate;

/**
 * 公告
 * Class NoticeValidate
 * @package app\admin\validate
 */
class NoticeValidate extends Validate
{
    protected $rule=array(
        'title'=>'require'
    );
    protected $message=array(
        'title'=>'请填写公告标题'
    );

}