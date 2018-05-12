<?php
namespace app\index\controller;

use app\common\model\AdvGroupModel;
use think\Db;

class IndexController extends BaseController
{
    public function index()
    {

        $this->seo();
        return $this->fetch();
    }

    public function test(){
        $pc = new \sdk\Prpcrypt('95673c3602bfdbf4612ee96b5ca024fd');


        return var_export($pc->decrypt('/0shz0VPA2vHvMAIP8O7FvwmxBrlgc2gF6SWM33wdEYGP697Ydy6zwb3O/2PAnQiaeWtUoK+ksER7nZhZSYouw==','10238852149'));
        return var_export($pc->encrypt('<xml></xml>','10238852149'));
    }
    public function test2(){
        $pc = new \sdk\AESEnctypt('95673c3602bfdbf4612ee96b5ca024fd');


        //return var_export($pc->decrypt('/0shz0VPA2vHvMAIP8O7FvwmxBrlgc2gF6SWM33wdEYGP697Ydy6zwb3O/2PAnQiaeWtUoK+ksER7nZhZSYouw==','10238852149'));
        return var_export($pc->encrypt('<xml></xml>','10238852149'));
    }

}
