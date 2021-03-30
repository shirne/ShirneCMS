<?php

namespace addon\base;

use think\facade\Log;

class BaseAddon{
    
    protected $addonName = '';
    protected $settings = [];

    public function __construct()
    {
        $class = array_pop(explode('\\', static::class));
        $this->addonName = substr($class, 0, -strlen('Addon'));
        $this->settings = getSetting($this->addonName);
        $this->init();
    }

    protected static $instances=[];
    public static function factory($name){
        if(isset(self::$instances[$name])){
            return self::$instances[$name];
        }
        $class = '\\addon\\'.from_camel_case($name).'\\'.$name.'Addon';
        try{
            self::$instances[$name] = new $class();
        }catch(\Exception $e){
            Log::record('Addon '.$name.' error:'.$e->getMessage());
            self::$instances[$name] = new self();
        }
        
        return self::$instances[$name];
    }

    protected function addPermission(){
        return false;
    }
    protected function addMenu(){
        return false;
    }

    public function getOrder(){
        return null;
    }

    public function init(){
        return false;
    }
    public function install(){
        return false;
    }
    public function task(){
        return false;
    }
    public function uninstall(){
        return false;
    }
}