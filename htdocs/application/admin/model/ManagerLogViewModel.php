<?php


namespace app\admin\model;
use think\model\ViewModel;

class ManagerLogViewModel extends ViewModel {
    public $viewFields = array(
        'manager_log'=>array('*'),
        'manager'=>array('username','realname', '_on'=>'manager_log.manager_id=manager.id')
    );
}