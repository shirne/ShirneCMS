<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/4/29
 * Time: 16:30
 */
namespace app\admin\validate;

use think\Validate;

class MemberLevelValidate extends Validate
{
    public function setId($id=0){
        foreach ($this->rule as $f=>$rule){
            $this->rule[$f]=str_replace($rule,'%id%',$id);
        }
    }
    protected $rule  = array(
        'level_name'=>'require|unique:memberLevel,%id%,level_id',
    );

    protected $message   = array(
        'level_name.require' => '请填写会员组名称',
        'level_name.unique' => '会员组名称已存在',
    );
}