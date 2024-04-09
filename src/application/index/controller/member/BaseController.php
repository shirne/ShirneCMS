<?php

namespace app\index\controller\member;


use app\index\controller\AuthedController;

/**
 * 会员中心基类
 * Class BaseController
 * @package app\index\controller\member
 */
class BaseController extends AuthedController
{
    public function initialize()
    {
        parent::initialize();
        $this->assign('navmodel', 'member');
    }
}
