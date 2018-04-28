<?php

function setLogin($user){
    $time=time();
    session('adminId',$user['id']);
    session('adminLTime',$time);
    session('adminname',empty($user['realname'])?$user['username']:$user['realname']);
    M('Manager')->where(array('id'=>$user['id']))->save(array(
        'login_ip'=>get_client_ip(),
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
    $menus=S('menus');
    if(empty($menus)){
        $list=M('permission')->where(array('disable'=>0))->order('parent_id ASC,order_id ASC,id ASC')->select();
        $menus=array();
        foreach ($list as $item){
            $menus[$item['parent_id']][]=$item;
        }

        S('menus',$menus,1800);
    }
    return $menus;
}

function FU($url='',$vars=''){

    $link=U($url,$vars);

    return str_replace(__APP__,'',$link);
}
//end file