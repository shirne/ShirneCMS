<?php
/**
 * 已被授权的控制器基类.
 * User: shirne
 * Date: 2018/4/10
 * Time: 下午10:05
 */

namespace app\api\Controller;


class AuthedController extends BaseController
{
    public function initialize(){
        parent::initialize();
        if(!$this->isLogin){
            $this->response('未登录',101);
        }
    }
}