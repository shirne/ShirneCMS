<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirn
 * Date: 2016/9/14
 * Time: 12:16
 */

namespace app\admin\controller;


class NoticeController extends BaseController
{
    public function index($type='')
    {
        $model = Db::name('Notice');
        $where=array();
        if(!empty($type )){
            $where['type'] = $type;
        }

        $this->pagelist($model,$where,'id DESC');
        $this->display();
    }

    /**
     * 公告添加
     */
    public function add()
    {
        //默认显示添加表单
        if (!$this->request->isPost()) {
            $this->display();
        }
        if ($this->request->isPost()) {
            //如果用户提交数据
            $model = D("Notice");
            if (!$model->create()) {
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                $this->error($model->getError());
                exit();
            } else {
                $model->create_at=time();
                $model->update_at=$model->create_at;
                $model->manager_id=session('adminId');
                if ($model->add()) {
                    $this->success("添加成功", url('Notice/index'));
                } else {
                    $this->error("添加失败");
                }
            }
        }
    }
    /**
     * 公告修改
     */
    public function update($id)
    {
        $id = intval($id);
        //默认显示添加表单
        if (!$this->request->isPost()) {
            $model = Db::name('Notice')->where("id= %d",$id)->find();
            $this->assign('model',$model);
            $this->display();
        }
        if ($this->request->isPost()) {
            $model = D("Notice");
            if (!$model->create()) {
                $this->error($model->getError());
            }else{
                $model->update_at=time();
                if ($model->save()) {
                    $this->success("更新成功", url('Notice/index'));
                } else {
                    $this->error("更新失败");
                }
            }
        }
    }
    /**
     * 公告删除
     */
    public function delete($id)
    {
        $id = intval($id);
        $model = Db::name('Notice');
        $result = $model->delete($id);
        if($result){
            $this->success("删除成功", url('Notice/index'));
        }else{
            $this->error("删除失败");
        }
    }

}