<?php
namespace app\index\controller;

use app\common\model\AdvGroupModel;
use app\common\model\ArticleModel;
use think\Db;

class IndexController extends BaseController
{
    public function index()
    {
        $this->seo();
        return $this->fetch();
    }

}
