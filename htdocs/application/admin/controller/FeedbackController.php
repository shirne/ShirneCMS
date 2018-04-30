<?php
namespace app\admin\controller;
use app\admin\model\FeedbackModel;
use app\index\validate\FeedbackValidate;
use think\Db;

/**
 * 留言管理
 */
class FeedbackController extends BaseController
{

    /**
     * 留言列表
     * @param string $key
     */
    public function index($key="")
    {
        $model=Db::view('Feedback','*')
            ->view('Member',['username','realname'],'Feedback.member_id=Member.id','LEFT')
            ->view('Manager',['username'=>'manager_username','realname'=>'manager_realname'],'Feedback.manager_id=Manager.id','LEFT');
        $where=array();
        if(!empty($key)){
            $where['feedback.email'] = array('like',"%$key%");
            $where['feedback.content'] = array('like',"%$key%");
            $where['_logic'] = 'or';
        }
        $lists=$model->where($where)->paginate(15);
        $this->assign('lists',$lists);
        $this->assign('page',$lists->render());
        return $this->fetch();
    }

    /**
     * 回复留言
     * @param $id
     */
    public function reply($id)
    {
        $id = intval($id);

        if ($this->request->isPost()) {
            $data = $this->request->only(['reply','status'],'post');
            $validate=new FeedbackValidate();
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }else{
                $data['reply_at']=time();
                if (FeedbackModel::update($data,['id'=>$id])) {
                    $this->success("更新成功", url('feedback/index'));
                } else {
                    $this->error("更新失败");
                }        
            }
        }
        $model = FeedbackModel::get($id);
        $this->assign('model',$model);
        return $this->fetch();
    }

    /**
     * 删除留言
     * @param $id
     */
    public function delete($id)
    {
        $id = intval($id);
        $model=FeedbackModel::get($id);
        $result = $model->delete();
        if($result){
            $this->success("留言删除成功", url('feedback/index'));
        }else{
            $this->error("留言删除失败");
        }
    }
}
