<?php
/**
 * 商品分类
 * User: shirne
 * Date: 2018/5/11
 * Time: 17:48
 */

namespace app\admin\controller;


class ProductCategoryController extends BaseController
{
    public function index(){

        return $this->fetch();
    }
}