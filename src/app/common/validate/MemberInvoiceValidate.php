<?php
namespace app\common\validate;

use think\Validate;

class MemberInvoiceValidate extends Validate
{
    protected $rule=[
        'title|公司名称'=>'require',
    ];
}