<?php

namespace app\admin\validate;


use think\Validate;

/**
 * 链接数据验证
 * Class LinksValidate
 * @package app\admin\validate
 */
class LinksValidate extends Validate
{
    protected $rule=array(
        'title'=>'require',
        'url'=>'require|url'
    );
    protected $message=array(
        'title'=>'请填写链接名称',
        'url.require'=>'请填写链接地址',
        'url'=>'链接地址格式错误',
    );
}