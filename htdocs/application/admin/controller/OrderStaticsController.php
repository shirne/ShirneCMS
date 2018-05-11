<?php
/**
 * 订单统计
 * User: shirne
 * Date: 2018/5/11
 * Time: 18:17
 */

namespace app\admin\controller;


class OrderStaticsController extends BaseController
{
    public function index(){

        return $this->fetch();
    }
}