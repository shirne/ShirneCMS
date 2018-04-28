<?php
namespace app\admin\controller;
/**
 * 留言管理
 */
class FeedbackController extends BaseController
{
    public function _initialize()
    {
        $this->table="feedback";
        $this->model=M($this->table);
        parent::_initialize();
    }

    /**
     * 留言列表
     */
    public function index($key="")
    {
        $model=D('FeedbackView');
        $where=array();
        if(!empty($key)){
            $where['feedback.email'] = array('like',"%$key%");
            $where['feedback.content'] = array('like',"%$key%");
            $where['_logic'] = 'or';
        }
        $this->pagelist($model,$where,'feedback.id DESC');
        $this->display();     
    }

    /**
     * 回复留言
     */
    public function reply($id)
    {
        $id = intval($id);
        //默认显示添加表单
        if (!IS_POST) {
            $model = $this->model->where("id= %d",$id)->find();
            $this->assign('model',$model);
            $this->display();
        }
        if (IS_POST) {
            $model = D($this->table);
            if (!$model->create()) {
                $this->error($model->getError());
            }else{
                $model->reply_at=time();
                if ($model->save()) {
                    $this->success("更新成功", U('feedback/index'));
                } else {
                    $this->error("更新失败");
                }        
            }
        }
    }
    /**
     * 删除留言
     */
    public function delete($id)
    {
        $id = intval($id);
        $result = $this->model->delete($id);
        if($result){
            $this->success("留言删除成功", U('feedback/index'));
        }else{
            $this->error("留言删除失败");
        }
    }
}
