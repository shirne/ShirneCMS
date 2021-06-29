<?php

namespace app\index\controller;


use extcore\traits\Upload;

/**
 * 需要登录的控制器基类
 * Class AuthedController
 * @package app\index\controller
 */
class AuthedController extends BaseController
{
    use Upload;

    public function initialize(){
        parent::initialize();

    }

    public function checkLogin()
    {
        parent::checkLogin();

        //如果没有的登录 重定向至登录页面
        if(empty($this->userid ) ) {
            if(!$this->request->isPost()) redirect()->remember();
            $this->error(lang('Pls login first'),url('index/login/index'));
        }

    }
}