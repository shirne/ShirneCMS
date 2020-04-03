<?php

namespace app\index\controller;

use app\common\facade\HelpCategoryFacade;
use think\Db;

class HelpController extends BaseController{

    public function initialize()
    {
        parent::initialize();
        $this->assign('navmodel','help');
    }
    public function index($name='',$id=0)
    {
        if($id>0){
            $help = Db::name('help')->where('status',1)->where('id' , $id)->find();
            $cate = HelpCategoryFacade::findCategory($help['cate_id']);
            if($name != $cate['name']){
                header('Location: '.url('index/help/index',['name'=>$cate['name'],'id'=>$id]));
                exit;
            }
        }elseif(!empty($name)){
            $cate = HelpCategoryFacade::findCategory($name);
            $help = Db::name('help')->where('status',1)->where('cate_id' , $cate['id'])->order('sort ASC,id ASC')->find();
            
        }else{
            $help = Db::name('help')->where('status',1)->order('sort ASC,id ASC')->find();
            $cate = HelpCategoryFacade::findCategory($help['cate_id']);
        }

        $lists = Db::name('help')->where('status',1)->order('sort ASC,id ASC')->field('id,title,vice_title,cate_id,sort,type')->select();

        $this->seo($help['title']);
        $this->assign('help',$help);
        $this->assign('helps',array_index($lists,'cate_id',true));
        $this->assign('category',$cate);
        $this->assign('cates',HelpCategoryFacade::getTreedCategory());
        
        return $this->fetch();
    }
}
