<?php

namespace app\admin\controller;


use app\admin\model\NoticeModel;
use app\admin\validate\NoticeValidate;
use think\Db;

/**
 * 公告管理
 * Class NoticeController
 * @package app\admin\controller
 */
class NoticeController extends BaseController
{
    /**
     * 公告列表
     * @param string $type
     * @return mixed
     */
    public function index($type='')
    {
        $model = Db::name('Notice');
        $where=array();
        if(!empty($type )){
            $where['type'] = $type;
        }

        $lists=$model->where($where)->order('ID DESC')->paginate(15);
        $this->assign('lists',$lists);
        $this->assign('page',$lists->render());
        return $this->fetch();
    }

    /**
     * 添加
     * @return mixed
     */
    public function add(){
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $validate = new NoticeValidate();
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                $data['manager_id'] = session('adminId');
                $model=NoticeModel::create($data);
                if ($model->getLastInsID()) {
                    $this->success("添加成功", url('Notice/index'));
                } else {
                    $this->error("添加失败");
                }
            }
        }
        $model=array();
        $this->assign('model',$model);
        $this->assign('id',0);
        return $this->fetch('edit');
    }

    /**
     * 编辑
     * @param $id
     * @return mixed
     */
    public function edit($id)
    {
        if ($this->request->isPost()) {
            $data=$this->request->post();
            $validate=new NoticeValidate();
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                $model=NoticeModel::get($id);

                if ($model->allowField(true)->save($data)) {
                    $this->success("更新成功", url('Notice/index'));
                } else {
                    $this->error("更新失败");
                }
            }
        }
        $model = Db::name('Notice')->where('id', $id)->find();
        if(empty($model)){
            $this->error('公告不存在');
        }
        $this->assign('model',$model);
        $this->assign('id',$id);
        return $this->fetch();
    }

    /**
     * 删除
     * @param $id
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