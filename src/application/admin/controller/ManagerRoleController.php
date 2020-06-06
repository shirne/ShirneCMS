<?php

namespace app\admin\controller;

use app\admin\model\ManagerRoleModel;
use app\admin\validate\ManagerRoleValidate;
use think\Db;

/**
 * 角色管理
 * Class ManagerRoleController
 * @package app\admin\controller
 */
class ManagerRoleController extends BaseController
{
    /**
     * 角色列表
     * @param string $key
     * @return mixed|\think\response\Redirect
     */
    public function index($key="")
    {
        if($this->request->isPost()){
            return redirect(url('',['key'=>base64url_encode($key)]));
        }
        $key=empty($key)?"":base64url_decode($key);
        $model=Db::name('ManagerRole');
        
        if(!empty($key )){
            $model->whereLike('role_name|type',"%$key%");
        }

        $lists=$model->order('type ASC')->paginate(15);
        $counts = Db::name('manager')->group('type')->field('count(id) as total_count,type')->select();
        $this->assign('counts',array_column($counts,'total_count','type'));
        $this->assign('lists',$lists);
        $this->assign('page',$lists->render());
        return $this->fetch();
    }
    
    /**
     * 添加
     * @return mixed
     */
    public function add()
    {
        if ($this->request->isPost()) {
            
            $data = $this->request->post();
            $validate=new ManagerRoleValidate();
            $validate->setId();
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                if($this->manager['type'] > $data['type']){
                    $this->error('您没有权限添加高级别的角色');
                }
                $model=ManagerRoleModel::create($data);
                if ($model->id) {
                    user_log($this->mid,'addmanagerrole',1,'添加角色'.$model->id ,'manager');
                    $this->success(lang('Add success!'), url('manager/index'));
                } else {
                    $this->error(lang('Add failed!'));
                }
            }
        }
        $maxtype=(int)Db::name('ManagerRole')->max('type');
        $model=array('type'=>$maxtype+1,'global'=>[],'detail'=>[]);
        $this->assign('model',$model);
        $this->assign('perms',config('permisions.'));
        $this->assign('styles',getTextStyles());
        return $this->fetch('update');
    }
    
    /**
     * 修改
     * @param $id
     * @return mixed
     */
    public function update($id)
    {
        $id=intval($id);
        if($id==0)$this->error('参数错误');
        $model=ManagerRoleModel::get($id);
        if($this->manager['type']>$model['type']){
            $this->error('您没有权限查看该角色');
        }
        
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $validate=new ManagerRoleValidate();
            $validate->setId($id);
            if (!$validate->scene('edit')->check($data)) {
                $this->error($validate->getError());
            }else{
                
                if($this->manager['type']>$data['type']){
                    $this->error('您不能将该角色设置为比您级别高的角色');
                }
                
                //更新
                if ($model->allowField(true)->update($data)) {
                    user_log($this->mid,'editmanagerrole',1,'修改管理员'.$model->id ,'manager');
                    $this->success(lang('Update success!'), url('manager_role/index'));
                } else {
                    $this->error(lang('Update failed!'));
                }
            }
        }
        
        $this->assign('model',$model);
        $this->assign('perms',config('permisions.'));
        $this->assign('styles',getTextStyles());
        return $this->fetch();
    }
    
    /**
     * 删除角色
     * @param $id
     */
    public function delete($id)
    {
        $id = intval($id);
        if(1 == $id) {
            $this->error("不可删除!");
        }
        
        $role = ManagerRoleModel::get($id);
        if ($this->manager['type']>=$role['type']) {
            $this->error('您不能删除该角色');
        }
        
        $count=Db::name('manager')->where('type',$id)->count();
        if ($count>0) {
            $this->error('请先将管理员移出此角色再进行删除');
        }
        
        $deleted=Db::name('managerRole')->where('id',$id)->delete();
        if($deleted){
            $this->success(lang('Delete success!'), url('manager_role/index'));
        }else{
            $this->error(lang('Delete failed!'));
        }
    }
}