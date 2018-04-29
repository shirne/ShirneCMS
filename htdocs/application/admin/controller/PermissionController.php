<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2017/5/8
 * Time: 7:56
 */

namespace app\admin\controller;


class PermissionController extends BaseController
{
    /**
     * 权限列表
     */
    public function index()
    {
        $lists=getMenus();
        $this->assign('model', $lists);
        $this->display();
    }

    public function clearcache(){
        cache('menus',null);
        $this->success("清除成功", url('permission/index'));
    }

    /**
     * 添加权限
     */
    public function add()
    {
        $this->assign('pid',I('pid',0));
        //默认显示添加表单
        if (!$this->request->isPost()) {
            $this->display();
        }
        if ($this->request->isPost()) {
            //如果用户提交数据
            $model = D("Permission");
            if (!$model->create()) {
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                $this->error($model->getError());
                exit();
            } else {
                if ($model->add()) {
                    $this->success("添加成功", url('permission/index'));
                } else {
                    $this->error("添加失败");
                }
            }
        }
    }
    /**
     * 更新权限信息
     * @param $id int|string
     */
    public function update($id)
    {
        $id = intval($id);
        //默认显示添加表单
        if (!$this->request->isPost()) {
            $model = Db::name('permission')->where("id=%d",$id)->find();
            $this->assign('perm',$model);
            $this->display();
        }
        if ($this->request->isPost()) {
            $model = D("Permission");
            if (!$model->create()) {
                $this->error($model->getError());
            }else{
                if ($model->save()) {
                    $this->success("更新成功", url('permission/index'));
                } else {
                    $this->error("更新失败");
                }
            }
        }
    }
    /**
     * 删除权限
     * @param $id int|string
     */
    public function delete($id)
    {
        $id = intval($id);
        $model = Db::name('Permission');
        $result = $model->where("id=%d",$id)->delete();
        if($result){
            $this->success("删除成功", url('permission/index'));
        }else{
            $this->error("删除失败");
        }
    }
}