<?php

namespace app\api\controller;

use think\facade\Log;

/**
 * 扩展
 * Class AddonController
 * @package app\api\controller
 */
class AddonController extends BaseController
{
    protected $controller;
    protected $action;

    protected $addon;

    public function index($addon, $controller = 'index', $action = 'index', $arguments = [])
    {

        $this->controller = $controller;
        $this->action = $action;

        $class = '\\addon\\'.$addon.'\\api\\controller\\'.ucfirst($controller).'Controller';
        if(!class_exists($class)){
            Log::record('Addon 接口定位错误: '.$class);
            $this->error('接口不存在');
        }
        $this->addon = new $class($this);
        $method = new \ReflectionMethod($this->addon, $action);
        $args = [];
        foreach ($method->getParameters() as $param) {
            if( isset($arguments[$param->name])){
                $args[] = $arguments[$param->name];
            } elseif ($this->request->has($param->name)) {
                $args[] = $this->request->param($param->name);
            } elseif ($param->isDefaultValueAvailable()) {
                $args[] = $param->getDefaultValue();
            }
        }
        
        return $method->invokeArgs($this->addon, $args);
    }

    public function __callProtected($method, $arguments){
        if(empty($this->addon) || !$this->addon instanceof \addon\base\BaseController){
            $this->error('页面不存在');
        }
        return call_user_func_array([$this, $method], $arguments);
    }

    public function __getProtected($name){
        if(empty($this->addon) || !$this->addon instanceof \addon\base\BaseController){
            $this->error('页面不存在');
        }
        return $this->$name;
    }
}