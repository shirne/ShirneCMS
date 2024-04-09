<?php

namespace app\admin\validate;

use app\common\core\BaseUniqueValidate;

/**
 * Class WechatValidate
 * @package app\admin\validate
 */
class WechatValidate extends BaseUniqueValidate
{
    protected $rule = array(
        'title' => 'require|unique:wechat,%id%',
        'appid' => 'require|unique:wechat,%id%'
    );
    protected $message = array(
        'title.require' => '请填写名称',
        'title.unique' => '公众号名称不可重复',
        'appid.require' => '请填写appid',
        'appid.unique' => 'appid不可重复',
    );
}
