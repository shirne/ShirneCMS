<?php

namespace app\common\validate;


use think\Validate;

class MemberMessageValidate extends Validate
{
    protected $rule = [
        'member_id|收信人' => 'require',
        'title|标题' => 'require',
        'content|内容' => 'require'
    ];
}
