<?php


namespace app\admin\model;

use think\model\ViewModel;

class MemberLogViewModel extends ViewModel {
    public $viewFields = array(
        'member_log'=>array('*'),
        'member'=>array('username','realname', '_on'=>'member_log.member_id=member.id')
    );
}