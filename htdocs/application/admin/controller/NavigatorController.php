<?php

namespace app\admin\controller;

use app\common\facade\CategoryFacade;
use app\common\facade\ProductCategoryFacade;
use think\Db;
use think\facade\Env;

/**
 * 导航管理
 * Class NavigatorController
 * @package app\admin\controller
 */
class NavigatorController extends BaseController
{
    /**
     * 编辑导航
     * @param string $model
     * @return mixed
     */
    public function index($model='index')
    {
        $path=Env::get('config_path').'/'.$model.'/navigator.php';
        $navigator=include($path);
        $modules=[
            'Index'=>'首页',
            'Page'=>'单页模块',
            'Article'=>'文章模块',
            'Product'=>'产品模块'
        ];
        if($this->request->isPost()){
            $this->checkPermision("navigator_update");
            $data=$this->request->post();
            $newNavigator=[];
            foreach ($data['navs'] as $v){
                if($v['urltype']=='module'){
                    $v['url']=[];
                    $v['url'][0]=$v['module'].'/index';

                    if(!empty($v['cate_name'])){
                        if($v['module']=='Page'){
                            if(strpos($v['cate_name'],'/')>0){
                                $parts = explode('/',$v['cate_name']);
                                $v['cate_name'] = $parts[0];
                                $v['url'][1]=['group'=>$parts[0],'name'=>$parts[1]];
                            }else{
                                $v['url'][1]=['group'=>$v['cate_name']];
                            }
                        }else{
                            $v['url'][1]=['name'=>$v['cate_name']];
                        }
                    }
                }
                if($v['subnavtype']=='module'){
                    $v['subnav']=$v['module'].'/'.$v['cate_name'];
                }

                unset($v['urltype']);
                unset($v['subnavtype']);
                unset($v['module']);
                unset($v['cate_name']);
                $newNavigator[]=$v;
            }

            file_put_contents($path,'<?php'."\nreturn ".var_export($newNavigator,true).';');
            cache($model.'_navigator',null);
            user_log($this->mid,'navigator',1,'修改导航'.$model ,'manager');
            $this->success('导航已更新',url('navigator/index'));
        }

        foreach ($navigator as $k=>$item){
            if(is_string($item['url'])){
                if(preg_match('/('.implode('|',array_keys($modules)).')\/[\w\d]+/i',$item['url'])){
                    $item['url']=[$item['url']];
                }
            }
            if(is_array($item['url'])){
                $item['url'][2]=substr($item['url'][0],0,strpos($item['url'][0],'/'));
            }
            if(!empty($item['subnav'])) {
                if (is_string($item['subnav'])) {
                    $item['subnavtype']='module';
                    $item['subnav']=explode('/',$item['subnav']);
                }else{
                    $item['subnavtype']='customer';
                }
            }
            $navigator[$k]=$item;
        }

        $this->assign('model',$model);
        $this->assign('modules',$modules);
        $this->assign('navigator',$navigator);
        return $this->fetch();
    }

    /**
     * 获取模型分类
     * @param string $module
     * @return \think\response\Json
     */
    public function getCategories($module='Article'){
        $cates=[];
        switch ($module){
            case 'Page':
                $groups=getPageGroups();
                foreach ($groups as $group){
                    $cates[]=[
                        'id'=>-$group['id'],
                        'pid'=>0,
                        'title'=>$group['group_name'],
                        'name'=>$group['group'],
                        'html'=>$group['group_name']
                    ];
                    $pages=Db::name('page')->where('group',$group['group'])->order('sort ASC,id ASC')->select();
                    foreach ($pages as $page){
                        $cates[]=[
                            'id'=>$page['id'],
                            'pid'=>-$group['id'],
                            'title'=>$page['title'],
                            'name'=>$group['group'].'/'.$page['name'],
                            'html'=>'|--'
                        ];
                    }
                }
                break;
            case 'Article':
                $cates=CategoryFacade::getCategories();
                break;
            case 'Product':
                $cates=ProductCategoryFacade::getCategories();
                break;
            default:
                break;
        }
        return json(['data'=>$cates,'code'=>1]);
    }
}