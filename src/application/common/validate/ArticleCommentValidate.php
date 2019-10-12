<?php

namespace app\common\validate;


use think\Validate;

class ArticleCommentValidate extends Validate
{
    protected $rule=[
        'article_id'=>'require',
        'content'=>'require'
    ];
}