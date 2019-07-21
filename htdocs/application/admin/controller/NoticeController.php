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
                $data['manager_id'] = $this->mid;
                $model=NoticeModel::create($data);
                if ($model['id']) {
                    $this->success(lang('Add success!'), url('Notice/index'));
                } else {
                    $this->error(lang('Add failed!'));
                }
            }
        }
        $model=array('status'=>1);
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
                    $this->success(lang('Update success!'), url('Notice/index'));
                } else {
                    $this->error(lang('Update failed!'));
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
     * 发布
     * @param $id
     * @param int $status
     */
    public function status($id,$status=0)
    {
        $data['status'] = $status==1?1:0;

        $result = Db::name('Notice')->whereIn("id",idArr($id))->update($data);
        if ($result && $data['status'] === 1) {
            user_log($this->mid,'pushnotice',1,'发布公告 '.$id ,'manager');
            $this -> success("发布成功", url('Notice/index'));
        } elseif ($result && $data['status'] === 0) {
            user_log($this->mid,'cancelnotice',1,'撤销公告 '.$id ,'manager');
            $this -> success("撤销成功", url('Notice/index'));
        } else {
            $this -> error("操作失败");
        }
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
            $this->success(lang('Delete success!'), url('Notice/index'));
        }else{
            $this->error(lang('Delete failed!'));
        }
    }

}