<?php

namespace app\index\controller;


use think\Db;

class PageController extends BaseController{

    public function index($name='')
    {

        $lists=Db::name('page')->field('id,name,title')->order('id ASC')->select();
        if(empty($lists))$this->error('页面不存在');
        if(empty($name)){
            $name=$lists[0]['name'];
        }

        $page=Db::name('page')->where(array('id|name'=>$name))->find();
        if(empty($page))$this->error('页面不存在');

        $this->seo($page['title']);
        $this->assign('page',$page);
        $this->assign('lists',$lists);
        return $this->fetch();
    }

    public function __call($method,$args){
        $this->index($method);
    }
}
