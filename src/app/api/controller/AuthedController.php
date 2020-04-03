<?php


namespace app\api\controller;

/**
 * 已被授权的控制器基类
 * Class AuthedController
 * @package app\api\Controller
 */
class AuthedController extends BaseController
{
    public function initialize(){
        parent::initialize();
        if(!$this->isLogin){
            $this->error('未登录',ERROR_NEED_LOGIN);
        }
    }
}