<?php

use think\Db;

function setLogin($member){
    session('userid', $member['id']);
    session('username',empty($member['realname'])? $member['username']:$member['realname']);
    $time=time();
    session('logintime',$time);
    Db::name('member')->where(array('id'=>$member['id']))->update(array(
        'login_ip'=>request()->ip(),
        'logintime'=>$time
    ));
    user_log($member['id'], 'login', 1, '登录成功');
}

function clearLogin($log=true){
    $id=session('userid');
    if($log && !empty($id)) {
        user_log($id, 'logout', 1, '退出登录');
    }

    session('userid',null);
    session('username',null);
    session('logintime',null);
}


function parseNavigator(&$config,$model){
    $navigators=cache($model.'_navigator');
    if(empty($navigators)){
        $navigators=array();
        foreach ($config as $item){
            if(empty($item['url']))continue;
            $item['url']=parseNavUrl($item['url'],$model);

            if(isset($item['subnav']) ){
                if(is_string($item['subnav'])) {
                    $args = explode('/', $item['subnav'].'/');
                    $item['subnav']=call_user_func('parseNav'.$args[0],$args[1],$model);
                }elseif(is_array($item['subnav'])){
                    foreach ($item['subnav'] as $k=>$sitem){
                        if(!empty($sitem['url'])){
                            $item['subnav'][$k]['url']=parseNavUrl($sitem['url'],$model);
                        }
                    }
                }
            }
            $navigators[]=$item;
        }
        cache($model.'_navigator',$navigators,['expire'=>7200]);
    }
    return $navigators;
}
function parseNavUrl($url,$model){
    if(is_array($url)){
        $url[0]=$model.'/'.strtolower($url[0]);
        return call_user_func_array('url',$url);
    }elseif(is_string($url)) {
        if (strpos($url, 'http://') === 0 ||
            strpos($url, 'https://') === 0 ||
            strpos($url, '/') === 0) {
            return $url;
        } else {
            $url=$model.'/'.strtolower($url);
            return url($url);
        }
    }
    return $url;
}

function parseNavPage($group,$model){
    $model=Db::name('Page')->where('status',1);

    if(!empty($group)){
        $model->where('group',$group);
    }
    $pages=$model->select();
    $subs=[];
    foreach ($pages as $page){
        $subs[]=array(
            'title'=>$page['title'],
            'url'=>url($model.'/page/index',['name'=>$page['name'],'group'=>$page['group']])
        );
    }
    return $subs;
}
function parseNavProduct($cate,$model){
    $model=Db::name('ProductCategory')->where('name',$cate)->find();

    $subs=[];
    if(!empty($model)){
        $cates=Db::name('ProductCategory')->where('pid',$model['id'])->select();;
        foreach ($cates as $c){
            $subs[]=array(
                'title'=>$c['title'],
                'url'=>url($model.'/product/index',['name'=>$c['name']])
            );
        }
    }

    return $subs;
}
function parseNavArticle($cate,$model){
    $model=Db::name('Category')->where('name',$cate)->find();

    $subs=[];
    if(!empty($model)){
        $cates=Db::name('Category')->where('pid',$model['id'])->select();;
        foreach ($cates as $c){
            $subs[]=array(
                'title'=>$c['title'],
                'url'=>url($model.'/article/index',['name'=>$c['name']])
            );
        }
    }

    return $subs;
}

function getAdImage($tag,$default=''){
    $adrow=\app\common\model\AdvGroupModel::getAdList($tag,1);
    if(!empty($adrow)){
        return $adrow[0]['image'];
    }
    return $default;
}
//end file