<?php

namespace app\admin\validate;


use app\common\core\BaseUniqueValidate;

/**
 * 广告位资料验证
 * Class AdvGroupValidate
 * @package app\admin\validate
 */
class AdvGroupValidate extends BaseUniqueValidate
{
    protected $rule = array(
        'title|广告组名称' => 'require',
        'flag|调用标识' => 'require|unique:AdvGroup,%id%'
    );
}
