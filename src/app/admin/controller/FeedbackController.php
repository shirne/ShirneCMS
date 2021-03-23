<?php
namespace app\admin\controller;

use app\common\model\FeedbackModel;
use app\admin\validate\FeedbackValidate;
use think\facade\Db;

/**
 * 留言管理
 * Class FeedbackController
 * @package app\admin\controller
 */
class FeedbackController extends BaseController
{

    /**
     * 留言列表
     * @param string $key
     * @return mixed
     */
    public function index($key="")
    {
        if($this->request->isPost()){
            return redirect(url('',['key'=>base64url_encode($key)]));
        }
        $key=empty($key)?"":base64url_decode($key);
        $model=Db::view('Feedback','*')
            ->view('Member',['username','realname'=>'member_realname','nickname','avatar'],'Feedback.member_id=Member.id','LEFT')
            ->view('Manager',['username'=>'manager_username','realname'=>'manager_realname'],'Feedback.manager_id=Manager.id','LEFT');
            
        if(!empty($key)){
            $model->whereLike('feedback.email|feedback.content|member.nickname|member.username',"%$key%");
        }
        $lists=$model->order('Feedback.id desc')->paginate(15);
        $this->assign('lists',$lists);
        $this->assign('page',$lists->render());
        return $this->fetch();
    }

    /**
     * 回复留言
     * @param $id
     * @return mixed
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
                $model=FeedbackModel::find($id);
                if ($model->save($data)) {
                    $this->success(lang('Update success!'), url('feedback/index'));
                } else {
                    $this->error(lang('Update failed!'));
                }        
            }
        }
        $model = FeedbackModel::find($id);
        $this->assign('model',$model);
        $this->assign('member',Db::name('member')->where('id',$model['member_id'])->find());
        return $this->fetch();
    }

    /**
     * 统计
     * todo 统计数据
     * @return mixed
     */
    public function statics(){
        return $this->fetch();
    }

    /**
     * 留言状态
     * @param $id
     * @param int $status
     */
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
        $model=FeedbackModel::find($id);
        $result = $model->delete();
        if($result){
            $this->success(lang('Delete success!'), url('feedback/index'));
        }else{
            $this->error(lang('Delete failed!'));
        }
    }
}
