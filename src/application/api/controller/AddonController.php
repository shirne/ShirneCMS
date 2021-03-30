<?php

namespace app\api\controller;

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

    public function index($addon, $controller = 'index', $action = 'index')
    {

        $this->controller = $controller;
        $this->action = $action;

        $class = '\\addon\\'.$addon.'\\admin\\controller\\'.ucfirst($controller).'Controller';
        $this->addon = new $class($this);
        $method = new \ReflectionMethod($this->addon, $action);
        $arguments = [];
        foreach ($method->getParameters() as $param) {
            if ($this->request->has($param->name)) {
                $arguments[] = $this->request->param($param->name);
            } elseif ($param->isDefaultValueAvailable()) {
                $arguments[] = $param->getDefaultValue();
            }
        }
        
        return $method->invokeArgs($this->addon, $arguments);
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