<?php

namespace extcore;

use think\Container;

/**
 * Class SimpleFacade
 * @package extcore
 */
class SimpleFacade
{
    protected static $instance;
    protected static function createFacade(){
        if(empty(self::$instance)){
            $class=static::getFacadeClass();
            self::$instance=Container::getInstance()->invokeClass($class);
        }
        return self::$instance;
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