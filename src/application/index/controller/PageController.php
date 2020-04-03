<?php

namespace app\index\controller;


use think\Db;

class PageController extends BaseController{

    public function initialize()
    {
        parent::initialize();
        $this->assign('navmodel','page');
    }
    public function index($name='',$group='')
    {
        if(!empty($name)) {
            $page = Db::name('page')->where('status',1)->where('id|name' , $name)->find();
            if (empty($page)) $this->error('页面不存在');
            $group=$page['group'];
        }elseif(empty($group)){
            return $this->errorPage('页面不存在');
        }

        $model=Db::name('page');
        $groupset=null;
        if(!empty($group)){
            $model->where('group',$group);
            $groupset=Db::name('PageGroup')->where('group',$group)->find();
            $this->assign('navmodel','page-'.$group);
        }
        $lists=$model->field('id,name,group,icon,title,vice_title')->where('status',1)->order('sort ASC,id ASC')->select();
        if(empty($lists))$this->error('页面不存在');
        if(empty($page)){
            $page=Db::name('page')->where('status',1)->where('id|name' , $lists[0]['name'])->find();;
        }

        $this->seo($page['title']);
        $this->assign('page',$page);
        $this->assign('group',$groupset);
        $this->assign('lists',$lists);
        $this->assign('images',Db::name('PageImages')->where('page_id',$page['id'])->select());
        if($page['use_template']){
            if(!empty($groupset) && $groupset['use_template']){
                return $this->fetch($groupset['group'].'/'.$page['name']);
            }else{
                return $this->fetch('page/'.$page['name']);
            }
        }elseif(!empty($groupset) && $groupset['use_template']){
            return $this->fetch($groupset['group'].'/index');
        }
        return $this->fetch();
    }
}
