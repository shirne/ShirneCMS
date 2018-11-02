<?php

namespace app\admin\controller;


use app\admin\validate\PermissionValidate;
use think\Db;

/**
 * 菜单管理
 * Class PermissionController
 * @package app\admin\controller
 */
class PermissionController extends BaseController
{
    /**
     * 菜单列表
     */
    public function index()
    {
        $lists=getMenus();
        $this->assign('model', $lists);
        return $this->fetch();
    }

    /**
     * 清除缓存
     */
    public function clearcache(){
        cache('menus',null);
        $this->success("清除成功", url('permission/index'));
    }

    /**
     * 添加
     * @param $pid
     * @return mixed
     */
    public function add($pid){
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $validate = new PermissionValidate();
            $validate->setId();

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                if (Db::name('Permission')->insert($data)) {
                    $this->success(lang('Add success!'), url('permission/index'));
                } else {
                    $this->error(lang('Add failed!'));
                }
            }
        }
        $model=array('pid'=>$pid);
        $this->assign('perm',$model);
        return $this->fetch('edit');
    }

    /**
     * 修改
     * @param int $id
     * @return mixed
     */
    public function edit($id=0)
    {
        $id = intval($id);
        if ($this->request->isPost()) {
            $data=$this->request->post();
            $validate=new PermissionValidate();
            $validate->setId($id);

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                $data['id']=$id;
                if (Db::name('Permission')->update()) {
                    $this->success(lang('Update success!'), url('permission/index'));
                } else {
                    $this->error(lang('Update failed!'));
                }

            }
        }
            $model = Db::name('permission')->where('id' , $id)->find();
        if(empty($model)){
            $this->error('要编辑的项不存在');
        }
        $this->assign('perm',$model);
        return $this->fetch();
    }

    /**
     * 删除
     * @param $id int|string
     */
    public function delete($id)
    {
        $id = intval($id);
        $model = Db::name('Permission');
        $result = $model->where('id',$id)->delete();
        if($result){
            $this->success(lang('Delete success!'), url('permission/index'));
        }else{
            $this->error(lang('Delete failed!'));
        }
    }
}