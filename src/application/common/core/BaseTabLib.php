<?php

namespace app\common\core;


use think\template\TagLib;

/**
 * Class BaseTabLib
 * @package app\common\taglib
 */
class BaseTabLib extends TagLib
{
    protected function parseWhere($arg){
        $matched = preg_match('/^(?:(EQ|NEQ|GT|LT|EGT|ELT)\s+)?(.*)$/',$arg,$condition);
        if($matched && !empty($condition[1])){
            $map = [
                'EQ'=>'=',
                'NEQ'=>'<>',
                'GT'=>'>',
                'LT'=>'<',
                'EGT'=>'>=',
                'ELT'=>'<='
            ];
            $compare=isset($map[$condition[1]])?$map[$condition[1]]:$condition[1];
            return ["'$compare'",$this->parseArg($condition[2])];
        }
        return ["'='",$this->parseArg($arg)];
    }
    
    protected function parseArg($arg)
    {
        if(empty($arg)){
            return "''";
        }
        if(strpos($arg,'$')===0){
            //检测长度，以防超长字符串导致正则crash
            if(strlen($arg)<50 && preg_match('/^\\$[a-zA-Z_][a-zA-Z0-9_\[\]\']*$/',$arg)) {
                return $arg;
            }
        }elseif(is_numeric($arg)){
            return floatval($arg);
        }else{
            if(in_array(strtolower($arg),['true','false','null'])){
                return strtolower($arg);
            }else {
                return var_export(strval($arg), true);
            }
        }
        return "''";
    }

    protected function exportArg($args,$ignores=['var'])
    {
        $dump = '[';
        foreach ($args as $k=>$arg)
        {
            if(in_array($k,$ignores)!==false)continue;

            //$key 不合法
            if(strlen($k)>50 || !preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/',$k))continue;

            $dump.="'$k'=>".$this->parseArg($arg).',';
        }
        $dump .= ']';
        return $dump;
    }
}