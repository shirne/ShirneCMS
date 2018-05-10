<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/5/5
 * Time: 9:33
 */

namespace app\common\validate;


use think\Validate;

class ArticleCommentValidate extends Validate
{
    protected $rule=[
        'article_id'=>'require',
        'content'=>'require'
    ];
}