<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/5/12
 * Time: 22:57
 */

namespace app\api\Controller;


use think\Db;

class MemberController extends AuthedController
{
    public function profile(){
        return $this->response(Db::name('member')->find($this->user['id']));
    }
}