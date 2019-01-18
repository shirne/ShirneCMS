<?php

namespace app\admin\validate;


use app\common\validate\BaseUniqueValidate;

/**
 * 设置表数据验证
 * Class SettingValidate
 * @package app\admin\validate
 */
class SettingValidate extends BaseUniqueValidate
{
    protected $rule=array(
        'key'=>'require|unique:setting,%id%'
    );
    protected $message=array(
        'key.require'=>'请填写配置名',
        'key.unique'=>'配置名已经存在',
        'title.require'=>'请填写配置标题',
        'value.require'=>'请填写配置值'
    );
}