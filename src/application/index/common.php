<?php

use app\common\facade\CategoryFacade;
use think\Db;
use think\facade\Route;

function indexurl($channel_name){
    return url('index/channel/list', ['channel_name'=>$channel_name]);
}
function listurl($cate_name, $channel_name = ''){
    if(empty($channel_name)){
        $topCate = CategoryFacade::getTopCategory($cate_name);
        if(!empty($topCate)){
            $channel_name = $topCate['name'];
        }else{
            $channel_name = $cate_name;
        }
    }
    return url('index/channel/list', ['channel_name'=>$channel_name, 'cate_name'=>$cate_name]);
}
function viewurl($art, $channel_name = ''){
    if(empty($channel_name)){
        if(!empty($art['channel_name'])){
            $channel_name = $art['channel_name'];
        }elseif(!empty($art['category_name'])){
            $topCate = CategoryFacade::getTopCategory($art['category_name']);
            if(!empty($topCate)){
                $channel_name = $topCate['name'];
            }
        }
        if(empty($channel_name)){
            $channel_name = $art['category_name'];
        }
    }

    // name为空则取id
    $name = empty($art['name'])?('a-'.$art['id']):$art['name'];
    return url('index/channel/view', ['channel_name'=>$channel_name, 'cate_name'=>$art['category_name'], 'article_name'=>$name]);
}

function parseNavigator(&$config,$module){
    $navigators=cache($module.'_navigator');
    if(empty($navigators)){
        $navigators=array();
        foreach ($config as $item){
            if(empty($item['url']))continue;
            $item['model']=parseModel($item['url']);
            $item['url']=parseNavUrl($item['url'],$module);

            if(isset($item['subnav']) ){
                if(is_string($item['subnav'])) {
                    $args = explode('/', $item['subnav'].'/');
                    $item['subnav']=parseNavModel($args[1],$module,$args[0]);
                }elseif(is_array($item['subnav'])){
                    foreach ($item['subnav'] as $k=>$sitem){
                        if(!empty($sitem['url'])){
                            $item['subnav'][$k]['url']=parseNavUrl($sitem['url'],$module);
                        }
                    }
                }
            }
            $navigators[]=$item;
        }
        cache($module.'_navigator',$navigators,['expire'=>7200]);
    }
    return $navigators;
}
function parseModel($url){
    $model=[];
    if(is_array($url)){
        $url[0]=explode('/',$url[0]);
        $model[0]=strtolower($url[0][0]);
        if(!empty($url[1]['group']))$model[]=$url[1]['group'];
        if(!empty($url[1]['name'])){
            if($model[0] === 'article'){
                $category = CategoryFacade::findCategory($url[1]['name']);
                $topCate = CategoryFacade::getTopCategory($url[1]['name']);
                $model[]=$topCate['name'];
                if($category['id'] != $topCate['id']){
                    $model[]=$category['name'];
                }
            }else{
                $model[]=$url[1]['name'];
            }
        }
    }elseif(is_string($url)) {
        if (strpos($url, 'http://') !== 0 &&
            strpos($url, 'https://') !== 0 &&
            strpos($url, '/') !== 0) {
            $url=explode('/',$url);
            $model[0]=strtolower($url[0]);
        }
    }
    return implode('-',$model);
}
function parseNavUrl($url,$module){
    
    if(is_array($url)){
        if(count($url)>1 && strpos(strtolower($url[0]),'article/')===0 && isset($url[1]['name'])){
                $category = CategoryFacade::findCategory($url[1]['name']);
                $topCate = CategoryFacade::getTopCategory($url[1]['name']);
                if($category['id'] === $topCate['id']){
                    $url = [
                        'index/channel/index',
                        ['channel_name'=>$topCate['name']]
                    ];
                }else{
                    $url = [
                        'index/channel/list',
                        ['channel_name'=>$topCate['name'],'cate_name'=>$category['name']]
                    ];
                }
        }else{
            $url[0]=$module.'/'.strtolower($url[0]);
        }
        
        return call_user_func_array('url',$url);
    }elseif(is_string($url)) {
        if (strpos($url, 'http://') === 0 ||
            strpos($url, 'https://') === 0 ||
            strpos($url, '/') === 0) {
            return $url;
        } else {
            $url=$module.'/'.strtolower($url);
            return url($url);
        }
    }
    return $url;
}

function parseNavPage($group,$module){
    $model=Db::name('Page')->where('status',1);

    if(!empty($group)){
        $model->where('group',$group);
    }
    $pages=$model->select();
    $subs=[];
    foreach ($pages as $page){
        $subs[]=array(
            'title'=>$page['title'],
            'url'=>url($module.'/page/index',['name'=>$page['name'],'group'=>$page['group']])
        );
    }
    return $subs;
}
function parseNavModel($cate,$module,$modelName='Article'){
    if(strtolower($modelName)=='page')return parseNavPage($cate,$module);
    $cateModel=$modelName=='Article'?'Category':($modelName.'Category');
    if(empty($cate)){
        $model=['id'=>0];
    }else {
        $model = Db::name($cateModel)->where('name', $cate)->find();
    }

    $subs=[];
    if(!empty($model)){
        $cates=Db::name($cateModel)->where('pid',$model['id'])->select();;
        foreach ($cates as $c){
            if(strtolower($modelName) == 'article'){
                $url = listurl($c['name'], $cate);
            }else{
                $url = url($module.'/'.strtolower($modelName).'/index',['name'=>$c['name']]);
            }
            $subs[]=array(
                'title'=>$c['title'],
                'icon'=>$c['icon'],
                'url'=>$url
            );
        }
    }

    return $subs;
}

function is_nav($model,$curModel){
    if($model==$curModel){
        return true;
    }
    if(strpos($curModel,'-')>0){
        return is_nav($model,substr($curModel,0,strrpos($curModel,'-')));
    }
    return false;
}

function getAdImage($tag,$default=''){
    $adrow=\app\common\model\AdvGroupModel::getAdList($tag,1);
    if(!empty($adrow)){
        return $adrow[0]['image'];
    }
    return $default;
}

/**
 * 将action转为参数式绑定，以匹配路由的动态action绑定
 * @param string $url
 * @param string $vars
 * @param bool $suffix
 * @param bool $domain
 * @return string
 */
function aurl($url = '', $vars = '', $suffix = true, $domain = false){
    $part=explode('/',$url);
    if(count($part)<3)$part[2]='index';
    if(!is_array($vars))$vars=[];
    $vars['action']=$part[2];
    $part[2]=':action';
    return url(implode('/',$part),$vars,$suffix,$domain);
}

//end file