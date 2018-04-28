<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirn
 * Date: 2016/9/10
 * Time: 11:46
 */

namespace app\index\controller;


class AuthController extends BaseController
{
    public function _initialize(){
        parent::_initialize();

    }

    public function checkLogin()
    {
        parent::checkLogin();

        //如果没有的登录 重定向至登录页面
        if(empty($this->userid ) ) {
            redirect(U('Login/login'));
        }

    }
}