<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/5/5
 * Time: 9:33
 */

namespace app\common\validate;


use think\Validate;

class ProductCommentValidate extends Validate
{
    protected $rule=[
        'product_id'=>'require',
        'content'=>'require'
    ];
}