<?php

namespace app\api\controller;

use think\Db;

/**
 * 单页数据接口
 * Class PageController
 * @package app\api\Controller
 */
class PageController extends BaseController
{
    public function groups(){
        $groups=Db::name('PageGroup')->select();
        return $this->response($groups);
    }

    public function pages($group=''){
        $model=Db::name('page')->where('status',1);

        if($group){
            $model->where('group',$group);
        }
        $lists=$model->order('sort ASC,id ASC')->select();
        return $this->response($lists);
    }

    public function page($name){
        $page = Db::name('page')->where('id|name' , $name)->find();
        if (empty($page)) $this->error('页面不存在',0);
        $images=Db::name('PageImages')->where('page_id' , $page['id'])->select();

        return $this->response([
            'page'=>$page,
            'images'=>$images
        ]);
    }
}