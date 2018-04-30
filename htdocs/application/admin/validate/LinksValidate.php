<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/4/30
 * Time: 10:26
 */

namespace app\index\validate;


use think\Validate;

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