<?php


namespace app\admin\model;

use think\model\ViewModel;

class FeedbackViewModel extends ViewModel {
    public $viewFields = array(
        'feedback'=>array('*'),
        'member'=>array('username','realname', '_on'=>'feedback.member_id=member.id','_type'=>'LEFT'),
        'manager'=>array('username'=>'manager_username','realname'=>'manager_realname', '_on'=>'feedback.manager_id=manager.id','_type'=>'LEFT'),
    );
}