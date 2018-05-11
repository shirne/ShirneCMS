<?php
/**
 * 商品管理
 * User: shirne
 * Date: 2018/5/11
 * Time: 17:47
 */

namespace app\admin\controller;


class ProductController extends BaseController
{
    public function index(){

        return $this->fetch();
    }
}