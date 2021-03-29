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

    public function index($addon, $controller = 'index', $action = 'index')
    {

        $this->controller = $controller;
        $this->action = $action;

        $class = '\\addon\\'.$addon.'\\admin\\controller\\'.ucfirst($controller).'Controller';
        $addonController = new $class($this);
        return $addonController->$action();
    }

    public function public_error($msg = '', $code = 0, $data = '', $wait = 3, array $header = []){
        return $this->error($msg, $code, $data, $wait, $header);
    }

    public function public_success($data = '', $code = 1, $msg = '', $wait = 3, array $header = []){
        return $this->success($data, $code, $msg, $wait, $header);
    }

    public function public_response($data, $code = 1, $msg = ''){
        return $this->response($data, $code, $msg);
    }
}