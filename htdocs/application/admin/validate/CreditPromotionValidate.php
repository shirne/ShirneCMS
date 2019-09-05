<?php

namespace app\admin\validate;


use app\common\core\BaseUniqueValidate;

class CreditPromotionValidate extends BaseUniqueValidate
{
    protected $rule =   [
        'name'  => 'require|unique:creditPromotion,%id%'
    ];

    protected $message  =   [
        'name.require' => '名称必须填写',
        'name.unique'     => '策略名称重复'
    ];
}