<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/7/26
 * Time: 18:50
 */

namespace app\common\taglib;


use think\template\TagLib;

class BaseTabLib extends TagLib
{
    protected function parseArg($arg)
    {
        if(empty($arg)){
            return "''";
        }
        if(strpos($arg,'$')===0){
            return $arg;
        }elseif(preg_match('/^\d+\.$/',$arg)){
            return floatval($arg);
        }else{
            return "'$arg'";
        }
    }
}