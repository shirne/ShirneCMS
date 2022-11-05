<?php

use think\Db;
use think\facade\Request;

define('SESSKEY_ADMIN_ID','adminId');
define('SESSKEY_ADMIN_NAME','adminname');
define('SESSKEY_ADMIN_LAST_TIME','adminLTime');
define('SESSKEY_ADMIN_AUTO_LOGIN','adminlogin');

function getProductPresets($model=[]){
    return [
        '0'=>[
            'type'=>isset($model['type'])?$model['type']:1,
            'is_discount'=>isset($model['is_discount'])?$model['is_discount']:1,
            'is_commission'=>isset($model['is_commission'])?$model['is_commission']:1,
        ],
    ];
}

function showLevel($level,$isshort=false){
    if(!empty($level) && $level['level_id']>0){
        return '<span class="badge badge-'.$level['style'].'">'.($isshort?$level['short_name']:$level['level_name']).'</span>';
    }
    return '';
}

function showAgent($agent,$isshort=false){
    if(!empty($agent) && $agent['level_id']>0){
        return '<span class="badge badge-'.$agent['style'].'">'.($isshort?$agent['short_name']:$agent['name']).'</span>';
    }
    return '';
}

function setLogin($user, $logintype = 1){
    $time=time();
    session(SESSKEY_ADMIN_ID,$user['id']);
    session(SESSKEY_ADMIN_LAST_TIME,$time);
    session(SESSKEY_ADMIN_NAME,empty($user['realname'])?$user['username']:$user['realname']);
    Db::name('Manager')->where('id',$user['id'])->update(array(
        'login_ip'=>Request::ip(),
        'logintime'=>$time
    ));
    if($logintype == 1){
        user_log($user['id'],'login',1,'登录成功' ,'manager');
    }else{
        user_log($user['id'],'login',1,'自动登录成功' ,'manager');
    }
}

function clearLogin($log=true){
    $id=session(SESSKEY_ADMIN_ID);
    if($log && !empty($id)) {
        user_log($id, 'logout', 1, '退出登录');
    }

    session(SESSKEY_ADMIN_ID,null);
    session(SESSKEY_ADMIN_NAME,null);
    session(SESSKEY_ADMIN_LAST_TIME,null);
    cookie(SESSKEY_ADMIN_AUTO_LOGIN,null);
}

function getMenus(){
    $menus=cache('menus');
    if(empty($menus)){
        $list=Db::name('permission')->where('disable',0)->order('parent_id ASC,sort_id ASC,id ASC')->select();
        $menus=array();
        foreach ($list as $item){
            $menus[$item['parent_id']][]=$item;
        }

        cache('menus',$menus,1800);
    }
    return $menus;
}

function check_password($password){
    if(in_array($password,['123456','654321','admin','abc123','123abc','12345678','123456789'])){
        session('password_error',1);
    }elseif(preg_match('/^([0-9])\\1*$/',$password)){
        session('password_error',2);
    }elseif(preg_match('/^[0-9]*$/',$password)){
        session('password_error',3);
    }elseif(preg_match('/^([a-zA-Z])\\1*$/',$password)){
        session('password_error',4);
    }else{
        session('password_error',null);
    }
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
        $images = str_replace('\\','/',$images);

        if(strpos($images,'../') !== false)return;
        
        if(!empty($images) && strpos($images,'/uploads/')===0){
            @unlink('.'.$images);
        }
    }
}

function list_empty($col=5){
    return '<tr><td colspan="'.$col.'" class="text-center text-muted">暂时没有记录</td></tr>';
}

function ignore_array($val){
    if(is_array($val))return '';
    return strval($val);
}

function wechat_is_official($type){
    if(in_array($type,['wechat','subscribe','service'])){
        return true;
    }
    return false;
}


function save_setting( $data, $group=''){
    $settings=getSettings(true,false,false);
    $group = empty($group)?[]:explode(',',$group);
    foreach ($data as $k=>$v){
        if(substr($k,0,2)=='v-'){
            $key=substr($k,2);
            if(isset($settings[$key])) {
                if(!empty($group) && !in_array($settings[$key]['group'],$group)){
                    continue;
                }
                if (is_array($v)) {
                    if(in_array($settings[$key]['type'],['json','array'])){
                        if($settings[$key]['type'] == 'array'){
                            $v = array_values($v);
                        }
                        $v = json_encode($v,JSON_UNESCAPED_UNICODE);
                    }else {
                        $v = serialize($v);
                    }
                }
                
                if ($settings[$key]['value'] != $v) {
                    Db::name('setting')->where('key', $key)->update(array('value' => $v));
                }
            }
        }
    }
    cache('setting',null);
    return true;
}
//end file