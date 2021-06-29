<?php


namespace app\api\controller;

/**
 * 已被授权的控制器基类，继承此类可自动屏蔽未登录的会员访问
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