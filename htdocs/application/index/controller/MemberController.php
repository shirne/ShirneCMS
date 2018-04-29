<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirn
 * Date: 2016/9/10
 * Time: 16:13
 */

namespace app\index\controller;


class MemberController extends AuthController
{
    public function initialize()
    {
        parent::initialize();
    }

    /**
     * 会员中心
     */
    public function index(){
        $this->display();
    }

    /**
     * 个人资料
     */
    public function profile(){
        if($this->request->isPost()){

        }
        $this->display();
    }

    /**
     * 修改头像
     */
    public function avatar(){

        $this->display();
    }


    /**
     * 安全中心
     */
    public function security(){

    }
}