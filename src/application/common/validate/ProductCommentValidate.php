<?php

namespace app\common\validate;


use think\Validate;

class ProductCommentValidate extends Validate
{
    protected $rule = [
        'product_id' => 'require',
        'content' => 'require'
    ];
}
