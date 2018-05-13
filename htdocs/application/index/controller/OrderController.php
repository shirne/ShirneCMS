<?php
/**
 * 订单功能
 * User: shirne
 * Date: 2018/5/13
 * Time: 23:57
 */

namespace app\index\controller;


class OrderController extends BaseController
{
    /**
     * 确认下单
     */
    public function confirm()
    {
        if($this->request->isPost()){

        }
        return $this->fetch();
    }
}