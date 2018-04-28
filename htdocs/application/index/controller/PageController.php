<?php

namespace app\index\controller;


class PageController extends BaseController{

    public function index($name='')
    {

        $lists=M('page')->field('id,name,title')->order('id ASC')->select();
        if(empty($lists))$this->error('页面不存在');
        if(empty($name)){
            $name=$lists[0]['name'];
        }

        $page=M('page')->where(array('id|name'=>$name))->find();
        if(empty($page))$this->error('页面不存在');

        $this->seo($page['title']);
        $this->assign('page',$page);
        $this->assign('lists',$lists);
        $this->display();
    }

    public function __call($method,$args){
        $this->index($method);
    }
}
