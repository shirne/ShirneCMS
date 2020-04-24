<?php

namespace app\admin\controller;

use extcore\traits\Upload;
use app\BaseController as Controller;
use think\facade\Db;
use think\Exception;

/**
 * 后台基类
 * 自带基于方法名的权限验证
 * Class BaseController
 * @package app\admin\controller
 */
class BaseController extends Controller {

    use Upload;

    protected $errMsg;
    protected $table;
    protected $model;

    protected $mid;
    protected $manager;
    protected $permision;

    protected $viewData=[];
    
    /**
     * 后台控制器全局初始化
     * @param $needLogin
     * @throws Exception
     */
    public function initialize(){
        parent::initialize();
        
        if(!defined('SUPER_ADMIN_ID'))define('SUPER_ADMIN_ID',config('app.super_admin_id'));
        if(!defined('TEST_ACCOUNT'))define('TEST_ACCOUNT',config('app.test_account'));

        $this->mid = session(SESSKEY_ADMIN_ID);
    
        $controller=strtolower($this->request->controller());
        if($controller === 'login'){
            return;
        }
        
        //判断用户是否登陆
        if(empty($this->mid ) ) {
            $this->error(lang('Please login first!'),url('admin/login/index'));
        }
        $this->manager=Db::name('Manager')->find($this->mid);
        if(empty($this->manager)){
            clearLogin();
            $this->error(lang('Invalid account!'),url('admin/login/index'));
        }
        if($this->manager['logintime']!=session(SESSKEY_ADMIN_LAST_TIME)){
            clearLogin();
            $this->error(lang('The account has login in other places!'),url('admin/login/index'));
        }

        //$controller=strtolower($this->request->controller());
        if($controller!='index'){
            $action=strtolower($this->request->action());
            if($action != 'search') {
                if ($this->request->isPost() || $action == 'add' || $action == 'update') {
                    $this->checkPermision("edit");
                }
                if (strpos('del', $action) !== false || strpos('clear', $action) !== false) {
                    $this->checkPermision("del");
                }

                $this->checkPermision($controller . '_' . $action);
            }
        }

        if(!$this->request->isAjax()) {
            $this->assign('menus', getMenus());

            //空数据默认样式
            $this->assign('empty', list_empty());
        }
    }
    
    public function _empty(){
        
        $this->error('页面不存在',url('admin/index/index'));
    }

    /**
     * 检查权限
     * @param $permitem
     * @throws Exception
     */
    protected function checkPermision($permitem){
        if(!$this->getPermision($permitem)){
            $this->error(lang('You have no permission to do this operation!'));
        }
    }

    /**
     * 检查是否有权限
     * @param $permitem
     * @return bool
     * @throws Exception
     */
    protected function getPermision($permitem)
    {
        if($this->manager['type']==1){
            return true;
        }
        if(empty($this->permision)){
            $this->permision=Db::name('ManagerPermision')->where('manager_id',$this->mid)->find();
            if(empty($this->permision)){
                $this->error(lang('Bad permission settings, pls contact the manager!'));
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
    
    protected function setAutoIncrement($table, $incre){
        $incre = intval($incre);
        if($incre<1){
            $this->error('起始id必须大于1');
        }
        $maxid = Db::name($table)->max('id');
        if($incre<$maxid){
            $this->error('起始id必须大于当前数据的最大id :'.$maxid);
        }
    
        try {
            $succed = Db::execute('ALTER TABLE ' . config('database.prefix').$table . ' AUTO_INCREMENT = ' . intval($incre));
        }catch(Exception $e){
            $this->error($e->getMessage());
        }
        if($succed){
            user_log($this->mid,'set_increment',1,'设置['.$table.']起始id'.$incre,'manager');
            $this->success('设置成功');
        }else{
            $this->error('设置失败');
        }
    }

    /**
     * 兼容ajax的数据注册
     * @param mixed $name
     * @param string $value
     * @return $this
     */
    protected function assign($name, $value = '')
    {
        if($this->request->isAjax()) {
            if (is_array($name)) {
                $this->viewData = array_merge($this->viewData, $name);
            } else {
                $this->viewData[$name] = $value;
            }
        }else{
            parent::assign($name, $value);
        }

        return $this;
    }

    /**
     * 兼容ajax的输出
     * @param string $template
     * @param array $vars
     * @param array $config
     * @return string
     * @throws \Throwable
     */
    protected function fetch($template = '', $vars = [], $config = [])
    {
        if($this->request->isAjax()){
            $this->result($this->viewData,1);
        }

        return parent::fetch($template, $vars, $config);
    }

}