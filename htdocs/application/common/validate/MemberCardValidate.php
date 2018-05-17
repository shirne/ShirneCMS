<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/5/16
 * Time: 22:59
 */

namespace app\common\validate;


use think\Validate;

class MemberCardValidate extends Validate
{
    protected $rule=[
        'bank|银行'=>'require',
        'bankname|开户行'=>'require',
        'cardname|开户名'=>'require',
        'cardno|银行卡号'=>'require',
    ];
}