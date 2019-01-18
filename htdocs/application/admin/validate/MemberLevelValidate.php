<?php
namespace app\admin\validate;

use app\common\validate\BaseUniqueValidate;

/**
 * 会员等级资料验证
 * Class MemberLevelValidate
 * @package app\admin\validate
 */
class MemberLevelValidate extends BaseUniqueValidate
{
    protected $rule  = array(
        'level_name'=>'require|unique:memberLevel,%id%,level_id',
    );

    protected $message   = array(
        'level_name.require' => '请填写会员组名称',
        'level_name.unique' => '会员组名称已存在',
    );
}