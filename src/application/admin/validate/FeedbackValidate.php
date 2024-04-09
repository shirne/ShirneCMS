<?php

namespace app\admin\validate;


/**
 * 留言回复验证
 * Class FeedbackValidate
 * @package app\admin\validate
 */
class FeedbackValidate extends \think\Validate
{
    protected $rule =   [
        'reply'  => 'require|max:250'
    ];

    protected $message  =   [
        'reply.require' => '名称必须',
        'reply.max'     => '名称最多不能超过250个字符'
    ];
}
