<?php

/**
 * 唯一验证基类
 * @author : shirne
 * @date: 2018/4/30
 * @time: 11:33
 */

namespace app\common\core;

use think\Validate;

class BaseUniqueValidate extends Validate
{
    protected $originRule;
    public function setId($id=0){
        if(empty($this->originRule))$this->originRule=$this->rule;
        foreach ($this->originRule as $f=>$rule){
            $field=$f;
            if(strpos($f,'|')>0){
                $field=substr($f,0,strpos($f, '|'));
            }
            if(is_string($rule)) {
                if(strpos($rule,'%id%')!==false) {
                    if ($id == 0) {
                        $this->rule[$f] = str_replace('%id%', $field.',', $rule);
                    } else {
                        $this->rule[$f] = str_replace('%id%', $field . ',' . $id, $rule);
                    }
                }
            }elseif(is_array($rule)){
                if(isset($rule['unique'])){
                    if($id==0){
                        $rule['unique']=str_replace('%id%', $field.',', $rule['unique']);
                    }else{
                        $rule['unique']=str_replace('%id%', $field.','.$id, $rule['unique']);
                    }
                    $this->rule[$f]=$rule;
                }
            }
        }
    }
}