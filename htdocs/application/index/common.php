<?php

function setLogin($member){
    session('userid', $member['id']);
    session('username',empty($member['realname'])? $member['username']:$member['realname']);
    $time=time();
    session('logintime',$time);
    M('member')->where(array('id'=>$member['id']))->save(array(
        'login_ip'=>get_client_ip(),
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

function urllist($cate,$p=1){
    return $cate.'/p-'.$p.'.html';
}


function urlview($cate,$id,$p=1){
    return $cate.'/v-'.$id.($p==1?'':('-'.$p)).'.html';
}

//end file