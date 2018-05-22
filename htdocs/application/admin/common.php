<?php

use think\Db;
use think\facade\Request;

function setLogin($user){
    $time=time();
    session('adminId',$user['id']);
    session('adminLTime',$time);
    session('adminname',empty($user['realname'])?$user['username']:$user['realname']);
    Db::name('Manager')->where('id',$user['id'])->update(array(
        'login_ip'=>Request::ip(),
        'logintime'=>$time
    ));
    user_log($user['id'],'login',1,'登录成功' ,'manager');
}

function clearLogin($log=true){
    $id=session('adminId');
    if($log && !empty($id)) {
        user_log($id, 'logout', 1, '退出登录');
    }

    session('adminId',null);
    session('adminname',null);
    session('adminLTime',null);
}

function getMenus(){
    $menus=cache('menus');
    if(empty($menus)){
        $list=Db::name('permission')->where('disable',0)->order('parent_id ASC,order_id ASC,id ASC')->select();
        $menus=array();
        foreach ($list as $item){
            $menus[$item['parent_id']][]=$item;
        }

        cache('menus',$menus,1800);
    }
    return $menus;
}

function FU($url='',$vars=''){

    $link=url($url,$vars);

    return str_replace(app()->getModulePath(),'',$link);
}

function delete_image($images){
    if(is_array($images)){
        foreach ($images as $image){
            delete_image($image);
        }
    }else{
        if(!empty($images)){
            @unlink('.'.$images);
        }
    }
}
//end file