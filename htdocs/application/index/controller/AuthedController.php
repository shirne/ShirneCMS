<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirn
 * Date: 2016/9/10
 * Time: 11:46
 */

namespace app\index\controller;


use extcore\traits\Upload;

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
            redirect()->remember();
            $this->error('请先登录',url('index/login/index'));
        }

    }
}