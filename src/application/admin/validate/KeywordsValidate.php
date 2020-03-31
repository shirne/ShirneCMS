<?php

namespace app\admin\validate;


use app\common\core\BaseUniqueValidate;

/**
 * 关键字数据验证
 * Class KeywordsValidate
 * @package app\admin\validate
 */
class KeywordsValidate extends BaseUniqueValidate
{
    protected $rule=array(
        'title'=>'require|unique:keywords,%id%'
    );
    protected $message=array(
        'title'=>'请填写关键字',
        'title.unique'=>'关键字不可重复'
    );
}