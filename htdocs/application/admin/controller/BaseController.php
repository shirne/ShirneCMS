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
            redirect(url('Login/index'));
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
        $controller=strtolower(CONTROLLER_NAME);
        //if(strpos($controller,'/')!==false)$controller=substr($controller,strrpos($controller,'/')+1);
        if($controller!='index'){
            $action=strtolower(ACTION_NAME);
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

    /**
     * 分页统一处理
     * @param $model
     * @param array $where
     * @param string $order
     * @param bool $field
     * @param int $listRows
     */
    protected function pagelist($model,$where=array(),$order='',$field=true,$listRows=15){
        $options    =   array();
        $REQUEST    =   (array)I('request.');
        if(is_string($model)){
            $model  =   M($model);
        }

        $OPT        =   new \ReflectionProperty($model,'options');
        $OPT->setAccessible(true);

        $pk         =   $model->getPk();
        if($order===null){
            //order置空
        }else if ( isset($REQUEST['_order']) && isset($REQUEST['_field']) && in_array(strtolower($REQUEST['_order']),array('desc','asc')) ) {
            $options['order'] = '`'.$REQUEST['_field'].'` '.$REQUEST['_order'];
        }elseif( $order==='' && empty($options['order']) && !empty($pk) ){
            $options['order'] = $pk.' desc';
        }elseif($order){
            $options['order'] = $order;
        }
        unset($REQUEST['_order'],$REQUEST['_field']);

        if( !empty($where)){
            $options['where']   =   $where;
        }
        $options      =   array_merge( (array)$OPT->getValue($model), $options );
        $total        =   $model->where($options['where'])->count();

        if( isset($REQUEST['r']) ){
            $listRows = (int)$REQUEST['r'];
        }
        $page = new \Extend\Page($total, $listRows, $REQUEST);
        if($total>$listRows){
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        }
        $p =$page->show();
        $this->assign('page', $p? $p: '');
        $this->assign('total',$total);
        $options['limit'] = $page->firstRow.','.$page->listRows;

        $model->setProperty('options',$options);

        $this->assign('lists',$model->field($field)->select());
    }

    protected function uploadFile($folder,$field,$isreturn=false){
        $uploadpath='/Uploads/';
        $upload=new \Think\Upload(array(
            'mimes'         =>  array(), //允许上传的文件MiMe类型
            'maxSize'       =>  2000000, //上传的文件大小限制 (0-不做限制)
            'exts'          =>  array('jpg','jpeg','png','gif','bmp','tif','txt','csv','xls','doc','zip'), //允许上传的文件后缀
            'autoSub'       =>  true, //自动子目录保存文件
            'subName'       =>  array('date', 'Y/m'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
            'rootPath'      =>  '.'.$uploadpath, //保存根路径
            'savePath'      =>  $folder.'/', //保存路径
            'saveName'      =>  array('uniqid', '')
        ));
        if($file = $upload->uploadOne($_FILES[$field])){
            $file['url']=$uploadpath.$file['savepath'].$file['savename'];
            return $file;
        }else{
            $this->errMsg=$upload->getError();
            if($isreturn)return false;
            $this->error($this->errMsg);
        }
    }

    protected function upload($folder,$field,$isreturn=false){
        $uploadpath='/Uploads/';
        $upload=new \Think\Upload(array(
            'mimes'         =>  array(), //允许上传的文件MiMe类型
            'maxSize'       =>  2000000, //上传的文件大小限制 (0-不做限制)
            'exts'          =>  array('jpg','jpeg','png','gif','bmp','tif'), //允许上传的文件后缀
            'autoSub'       =>  true, //自动子目录保存文件
            'subName'       =>  array('date', 'Y/m'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
            'rootPath'      =>  '.'.$uploadpath, //保存根路径
            'savePath'      =>  $folder.'/', //保存路径
            'saveName'      =>  array('uniqid', '')
        ));
        if($file = $upload->uploadOne($_FILES[$field])){
            $file['url']=$uploadpath.$file['savepath'].$file['savename'];
            return $file;
        }else{
            $this->errMsg=$upload->getError();
            if($isreturn)return false;
            $this->error($this->errMsg);
        }
    }

}