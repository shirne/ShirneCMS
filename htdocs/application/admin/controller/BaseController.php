<?php

namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Request;

class BaseController extends Controller {


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
            $this->error('请登录',url('Login/index'));
        }
        $this->manage=Db::name('Manager')->find($this->mid);
        if(empty($this->manage)){
            clearLogin();
            $this->error('账号已失效',url('Login/index'));
        }
        if($this->manage['logintime']!=session('adminLTime')){
            clearLogin();
            $this->error('该账号在其它地方登录',url('Login/index'));
        }
        $controller=strtolower($this->request->controller());
        //if(strpos($controller,'/')!==false)$controller=substr($controller,strrpos($controller,'/')+1);
        if($controller!='index'){
            $action=strtolower($this->request->action());
            //if(strpos($action,'/')!==false)$action=substr($action,strrpos($action,'/')+1);
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
            $this->permision=Db::name('ManagerPermision')->where(array('manager_id'=>$this->mid))->find();
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

    protected function uploadFile($folder,$field,$isreturn=false,$is_img=false){
        $uploadpath='/uploads/';
        $config=array(
            'maxSize'       =>  2000000, //上传的文件大小限制 (0-不做限制)
            'exts'          =>  $is_img?array('jpg','jpeg','png','gif','bmp','tif'):array('jpg','jpeg','png','gif','bmp','tif','txt','csv','xls','doc','zip'), //允许上传的文件后缀
            'rootPath'      =>  '.'.$uploadpath, //保存根路径
            'savePath'      =>  $folder.'/', //保存路径
        );
        $file = $this->request->file($field);
        $path=$config['rootPath'].$config['savePath'].date('Y/m');
        if(!is_dir($path)){
            mkdir($path,0777,true);
        }
        $info = $file->validate(['size'=>$config['maxSize'],'ext'=>$config['exts']])->move( $path);
        if($info){
            $file['url']=$info->getSaveName();
            return $file;
        }else{
            $this->errMsg=$file->getError();
            if($isreturn)return false;
            $this->error($this->errMsg);
        }
    }

    protected function upload($folder,$field,$isreturn=false){
        return $this->uploadFile($folder,$field,$isreturn,true);
    }

}