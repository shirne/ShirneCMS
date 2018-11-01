<?php


namespace app\api\Controller;

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
            $this->response('未登录',ERROR_TOKEN_INVAILD);
        }
    }
}