<?php

namespace app\admin\validate;


use app\common\core\BaseUniqueValidate;

/**
 * Class WechatReplyValidate
 * @package app\admin\validate
 */
class WechatReplyValidate extends BaseUniqueValidate
{
    protected $rule=array(
        'wechat_id'=>'require',
        'title'=>'require|unique:wechatReply,%id%'
    );
    protected $message=array(
        'wechat_id.require'=>'必须指定公众号',
        'title.require'=>'请填写名称',
        'title.unique'=>'回复名称不可重复'
    );
}