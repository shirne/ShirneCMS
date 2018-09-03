<?php

namespace app\admin\controller;

use extcore\traits\Upload;
use think\Controller;
use think\Db;

class BaseController extends Controller {

    use Upload;

    protected $errMsg;
    protected $table;
    protected $model;

    protected $mid;
    protected $manage;
    protected $permision;

    public function initialize(){
        parent::initialize();

        $this->mid = session('adminId');
        //判断用户是否登陆
        if(empty($this->mid ) ) {
            $this->error('请登录',url('admin/login/index'));
        }
        $this->manage=Db::name('Manager')->find($this->mid);
        if(empty($this->manage)){
            clearLogin();
            $this->error('账号已失效',url('admin/login/index'));
        }
        if($this->manage['logintime']!=session('adminLTime')){
            clearLogin();
            $this->error('该账号在其它地方登录',url('admin/login/index'));
        }

        $controller=strtolower($this->request->controller());
        if($controller!='index'){
            $action=strtolower($this->request->action());
            if($this->request->isPost() || $action=='add' || $action=='update'){
                $this->checkPermision("edit");
            }
            if(strpos('del',$action)!==false || strpos('clear',$action)!==false){
                $this->checkPermision("del");
            }

            $this->checkPermision($controller.'_'.$action);
        }

        $this->assign('menus',getMenus());
    }
    protected function checkPermision($permitem){
        if($this->getPermision($permitem)==false){
            $this->error('您无权进行此操作');
        }
    }
    protected function getPermision($permitem)
    {
        if($this->manage['type']==1){
            return true;
        }
        if(empty($this->permision)){
            $this->permision=Db::name('ManagerPermision')->where('manager_id',$this->mid)->find();
            if(empty($this->permision)){
                $this->error('权限设置有误，请联系管理员');
            }
            $this->permision['global']=explode(',',$this->permision['global']);
            $this->permision['detail']=explode(',',$this->permision['detail']);
        }
        if(strpos($permitem,'_')>0){
            if(in_array($permitem,$this->permision['detail']))return true;
        }else{
            if(in_array($permitem,$this->permision['global']))return true;
        }
        return false;
    }

}