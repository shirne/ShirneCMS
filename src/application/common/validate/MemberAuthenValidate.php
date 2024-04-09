<?php

namespace app\common\validate;


use think\Validate;

class MemberAuthenValidate extends Validate
{
    protected $rule = [
        'realname|真实姓名' => 'require|min:2|max:4',
        'id_no|证件号码' => 'require|min:18|max:18',
        'image|证件人相面' => 'require',
        'image2|证件国徽面' => 'require',
    ];
}
