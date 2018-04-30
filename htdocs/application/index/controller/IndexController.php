<?php
namespace app\index\controller;

class IndexController extends BaseController
{
    public function index()
    {
        $this->seo();
        return $this->fetch();
    }

}
