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
            $field=$f;
            if(strpos($f,'|')>0){
                $field=substr($f,0,strpos($f, '|'));
            }
            if(is_string($rule)) {
                if($id==0){
                    $this->rule[$f] =str_replace('%id%', $field, $rule);
                }else{
                    $this->rule[$f] =str_replace('%id%', $field.','.$id, $rule);
                }
            }elseif(is_array($rule)){
                if(isset($rule['unique'])){
                    if($id==0){
                        $rule['unique']=str_replace('%id%', $field, $rule['unique']);
                    }else{
                        $rule['unique']=str_replace('%id%', $field.','.$id, $rule['unique']);
                    }
                }
                $this->rule[$f]=$rule;
            }
        }
    }
}