<?php

namespace extcore;

use think\Container;

/**
 * Class SimpleFacade
 * @package extcore
 */
class SimpleFacade
{
    protected static $bind=[];
    protected static function createFacade(){
        $class=static::class;
        if(empty(static::$bind[$class])){
            $class=static::getFacadeClass();
            static::$bind[$class]=Container::getInstance()->invokeClass($class);
        }
        return static::$bind[$class];
    }

    protected static function getFacadeClass()
    {
        return self::class;
    }

    public static function __callStatic($method, $params)
    {
        return call_user_func_array([static::createFacade(), $method], $params);
    }
}