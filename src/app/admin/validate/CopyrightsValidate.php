<?php

namespace app\admin\validate;


use app\common\core\BaseUniqueValidate;

/**
 * 版权署名数据验证
 * Class CopyrightsValidate
 * @package app\admin\validate
 */
class CopyrightsValidate extends BaseUniqueValidate
{
    protected $rule=array(
        'title'=>'require|unique:copyrights,%id%'
    );
    protected $message=array(
        'title'=>'请填写标题',
        'title.unique'=>'标题不可重复'
    );
}