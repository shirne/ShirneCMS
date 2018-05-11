<?php
/**
 * 订单管理
 * User: shirne
 * Date: 2018/5/11
 * Time: 17:47
 */

namespace app\admin\controller;


class OrderController extends BaseController
{
    public function index(){

        return $this->fetch();
    }
}