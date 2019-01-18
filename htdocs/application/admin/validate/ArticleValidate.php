<?php

namespace app\admin\validate;


use think\Validate;

/**
 * 文章资料验证
 * Class ArticleValidate
 * @package app\admin\validate
 */
class ArticleValidate extends Validate
{
    protected $rule=array(
        'title'=>'require'
    );
    protected $message=array(
        'title.require'=>'请填写文章标题'
    );

}