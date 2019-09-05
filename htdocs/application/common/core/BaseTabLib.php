<?php

namespace app\common\core;


use think\template\TagLib;

/**
 * Class BaseTabLib
 * @package app\common\taglib
 */
class BaseTabLib extends TagLib
{
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