<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/5/16
 * Time: 22:59
 */

namespace app\common\validate;


use think\Validate;

class MemberAddressValidate extends Validate
{
    protected $rule=[
        'recive_name|收货人姓名'=>'require',
        'mobile|联系电话'=>'require|mobile',
        'province|所在省份'=>'require',
        'city|所在城市'=>'require',
        'address|详细地址'=>'require'
    ];
}