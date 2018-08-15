<?php
namespace app\admin\controller;
use app\admin\model\FeedbackModel;
use app\admin\validate\FeedbackValidate;
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
                $model=FeedbackModel::get($id);
                if ($model->allowField(true)->save($data)) {
                    $this->success("更新成功", url('feedback/index'));
                } else {
                    $this->error("更新失败");
                }        
            }
        }
        $model = FeedbackModel::get($id);
        $this->assign('model',$model);
        $this->assign('member',Db::name('member')->where('id',$model['member_id'])->find());
        return $this->fetch();
    }

    public function statics(){
        return $this->fetch();
    }

    public function status($id,$status=0)
    {
        $data['status'] = intval($status);

        $result=FeedbackModel::whereIn('id',idArr($id))->update(['status'=>$status]);
        if ($result && $data['status'] === 1) {
            user_log($this->mid,'auditfeedback',1,'审核留言 '.$id ,'manager');
            $this -> success("审核成功", url('Feedback/index'));
        } elseif ($result && $data['status'] === 2) {
            user_log($this->mid,'hidefeedback',1,'隐藏留言 '.$id ,'manager');
            $this -> success("隐藏成功", url('Feedback/index'));
        } else {
            $this -> error("操作失败");
        }
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
