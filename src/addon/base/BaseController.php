<?php

namespace addon\base;


class BaseController {

    protected $moduleName;
    protected $controller;

    protected $request;

    public function __construct($controller)
    {
        $this->controller = $controller;
        $this->request = request();
        $this->initialize();
    }

    public function initialize(){
        return false;
    }

    public function __call($name, $arguments)
    {
        if(method_exists($this->controller, 'public_'.$name)){
            return call_user_func_array([$this->controller, 'public_'.$name], $arguments);
        }
        if(method_exists($this->controller, $name)){
            return call_user_func_array([$this->controller, $name], $arguments);
        }
        throw new \Exception('Methods '.$name.' not exists');
    }

    public function __get($name)
    {
        if(property_exists($this->controller, $name)){
            return $this->controller->$name;
        }
        throw new \Exception('Property '.$name.' not exists');
    }
}