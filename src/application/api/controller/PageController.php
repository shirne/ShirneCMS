<?php

namespace app\api\controller;

use think\Db;
use think\response\Json;

/**
 * 单页数据接口
 * Class PageController
 * @package app\api\Controller
 */
class PageController extends BaseController
{
    /**
     * 获取单页分组列表
     * @return Json 
     */
    public function groups(){
        $groups=Db::name('PageGroup')->select();
        return $this->response($groups);
    }

    /**
     * 获取指定分组的所有页面
     * @param string $group 
     * @return Json 
     */
    public function pages($group=''){
        $model=Db::name('page')->where('status',1);

        if($group){
            $model->where('group',$group);
        }
        $lists=$model->order('sort ASC,id ASC')->select();
        return $this->response($lists);
    }

    /**
     * 根据页面名称或id获取页面
     * @param int|string $name 
     * @return Json 
     */
    public function page($name){
        $page = Db::name('page')->where(is_numeric($name)?'id':'name' ,is_numeric($name)?intval($name):$name)->find();
        if (empty($page)) $this->error('页面不存在',0);
        $images=Db::name('PageImages')->where('page_id' , $page['id'])->select();

        return $this->response([
            'page'=>$page,
            'images'=>$images
        ]);
    }
}