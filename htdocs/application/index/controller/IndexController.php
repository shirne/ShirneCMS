<?php
namespace app\index\controller;

use app\common\model\AdvGroupModel;
use think\Db;

class IndexController extends BaseController
{
    public function index()
    {
        $banners=AdvGroupModel::getAdList('banner');
        $this->assign('banner',$banners);

        $this->seo();
        return $this->fetch();
    }

}
