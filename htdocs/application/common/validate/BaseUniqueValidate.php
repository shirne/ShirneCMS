<?php
/**
 * 唯一验证基类
 * User: shirne
 * Date: 2018/4/30
 * Time: 11:33
 */

namespace app\common\validate;

use think\Validate;

class BaseUniqueValidate extends Validate
{
    public function setId($id=0){
        foreach ($this->rule as $f=>$rule){
            if(is_string($rule)) {
                $this->rule[$f] = str_replace($rule, '%id%', $id);
            }elseif(is_array($rule)){
                foreach ($rule as $t=>$v){
                    $rule[$t]=str_replace($v, '%id%', $id);
                }
                $this->rule[$f]=$rule;
            }
        }
    }
}