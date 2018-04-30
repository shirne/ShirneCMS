<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/4/29
 * Time: 16:30
 */
namespace app\admin\validate;

use app\common\validate\BaseUniqueValidate;

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