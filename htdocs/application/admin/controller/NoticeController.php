<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirn
 * Date: 2016/9/14
 * Time: 12:16
 */

namespace app\admin\controller;


use app\admin\model\NoticeModel;
use app\index\validate\NoticeValidate;
use think\Db;

class NoticeController extends BaseController
{
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
     * 公告添加
     */
    public function edit($id=0)
    {
        if ($this->request->isPost()) {
            $data=$this->request->post();
            $validate=new NoticeValidate();
            if (!$validate->check($data)) {
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                $this->error($validate->getError());
            } else {
                if($id>0){

                    if (NoticeModel::update($data)) {
                        $this->success("更新成功", url('Notice/index'));
                    } else {
                        $this->error("更新失败");
                    }
                }else {
                    $data['manager_id'] = session('adminId');
                    if (NoticeModel::create($data)) {
                        $this->success("添加成功", url('Notice/index'));
                    } else {
                        $this->error("添加失败");
                    }
                }
            }
        }
        if($id>0) {
            $model = Db::name('Notice')->where(["id"=> $id])->find();
        }else{
            $model=array();
        }
        $this->assign('model',$model);
        return $this->fetch();
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