<?php

namespace app\api\Controller;


use think\Db;

/**
 * 会员操作接口
 * Class MemberController
 * @package app\api\Controller
 */
class MemberController extends AuthedController
{
    public function profile(){
        return $this->response(Db::name('member')->find($this->user['id']));
    }

    public function addresses(){

    }

    public function edit_address(){

    }

    public function change_password(){

    }

    public function sec_password(){

    }

    public function orders($status=''){

    }

    public function order_view($order_no){

    }
}