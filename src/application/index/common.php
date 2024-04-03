<?php

use think\Db;
use think\facade\Route;

define('SESSKEY_USER_AUTO_LOGIN','login');


function delete_image($images){
    if(is_array($images)){
        foreach ($images as $image){
            delete_image($image);
        }
    }else{
        if(!empty($images) && strpos($images,'/uploads/')===0){
            @unlink('.'.$images);
        }
    }
}

function explode_keywords($keywords){
    return array_filter(explode(',',str_replace(['，','、','；',';'],',',$keywords)));
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
        if(!empty($url[1]['name']))$model[]=$url[1]['name'];
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
        $url[0]=$module.'/'.strtolower($url[0]);
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
            $subs[]=array(
                'title'=>$c['title'],
                'icon'=>$c['icon'],
                'url'=>url($module.'/'.strtolower($modelName).'/index',['name'=>$c['name']])
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

function showstar($star, $max = 5){
    return implode('',[
        '<span class="stars">',
        str_repeat('<i class="ion-md-star"></i>',intval($star)),
        str_repeat('<i class="ion-md-star-half"></i>',ceil($star)-intval($star)),
        str_repeat('<i class="ion-md-star-outline"></i>',intval($max - $star)),
        '</span>'
    ]);
}


function orderclass($var, $up, $down){
    if($var == $up){
        return 'up';
    }elseif($var == $down){
        return 'down';
    }
    return 'cansort';
}
function trimItem($items){
    foreach($items as $k=> $values){
        $items[$k] = trim($values,', ');
    }
    return $items;
}
function pushQuery($items, $group, $id){
    $queries=[];
    foreach($items as $item){
        $queries[$item['group']].=$item['id'].',';
    }
    if(!isset($queries[$group]))$queries[$group]='';
    $queries[$group].=$id;

    return trimItem($queries);
}
function tripQuery($items, $group, $id){
    $queries=[];
    foreach($items as $item){
        if($item['group'] == $group && $item['id']==$id)continue;
        $queries[$item['group']].=$item['id'].',';
    }

    return trimItem($queries);
}
function tripItem($items, $group, $id){
    foreach($items as $k=>$item){
        if($item['group'] == $group && $item['id']==$id){
            unset($items[$k]);
            break;
        }
    }
    return $items;
}
//end file