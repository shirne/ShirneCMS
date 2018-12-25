<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/12/25
 * Time: 10:23
 */

namespace app\index\controller\member;


use app\index\controller\AuthedController;

class BaseController extends AuthedController
{
    public function initialize()
    {
        parent::initialize();
        $this->assign('navmodel','member');
    }
}