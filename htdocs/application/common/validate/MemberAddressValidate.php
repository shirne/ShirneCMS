<?php

namespace app\common\validate;


use think\Validate;

class MemberAddressValidate extends Validate
{
    protected $rule=[
        'recive_name|收货人姓名'=>'require',
        'mobile|联系电话'=>'require|telephone',
        'province|所在省份'=>'require',
        'city|所在城市'=>'require',
        'address|详细地址'=>'require'
    ];

    public function __construct(array $rules = [], array $message = [], array $field = [])
    {
        parent::__construct($rules, $message, $field);

        $this->defaultRegex['telephone']='/^(?:\d{3,4}-)?\d{7,8}|1[3-9][0-9]\d{8}$/';
        self::$typeMsg['telephone']=':attribute not a valid telephone';
    }
}