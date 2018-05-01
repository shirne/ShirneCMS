<?php
namespace app\admin\controller;
use app\common\model\MemberModel;
use app\common\validate\MemberValidate;
use think\Db;

/**
 * 用户管理
 */
class MemberController extends BaseController
{
    public function initialize()
    {
        parent::initialize();

        Db::name('Manager')->where(array('id'=>$this->manage['id']))->update(array('last_view_member'=>time()));
    }

    /**
     * 用户列表
     */
    public function index()
    {
        $model = Db::view('__MEMBER__ m','*');
        $where=array();
        $keyword=$this->request->request('keyword');
        if(!empty($keyword)){
            $where['m.username|m.email|m.realname'] = array('like',"%$keyword%");
        }

        $referer=$this->request->request('referer');
        if(!empty($referer)){
            if($referer!='0'){
                $member=$model->where(array('id|username'=>$referer))->find();
                if(empty($member)){
                    $this->error('填写的会员不存在');
                }
                $where['m.referer'] = $member['id'];
            }else {
                $where['m.referer'] = intval($referer);
            }
        }

        $lists=$model->view('__MEMBER__ rm',['username'=> 'refer_name','realname'=> 'refer_realname','isagent'=> 'refer_agent'],'m.referer=rm.id','LEFT')->paginate(15);

        $this->assign('lists',$lists);
        $this->assign('page',$lists->render());
        $this->assign('referer',$referer);
        $this->assign('keyword',$keyword);
        return $this->fetch();
    }
    public function set_agent($id=0){
        if(empty($id))$this->error('会员不存在');
        $member=Db::name('member')->find($id);
        if(empty($member))$this->error('会员不存在');

        $level=$this->request->get('level/d',1);
        if($level>3)$level=3;

        if($member['isagent']==$level)$this->success('设置成功');

        $data=array('isagent'=>$level);
        if(empty($member['agentcode']))$data['agentcode']=random_str(6);
        $result=Db::name('member')->where(array('id'=>$id))->update($data);
        if($result){
            user_log($this->mid,'setagent',1,'设置'.$level.'级代理 '.$id ,'manager');
            $this->success('设置成功');
            exit;
        }else{
            $this->error('设置失败');
        }
    }
    public function cancel_agent($id=0){
        if(empty($id))$this->error('会员不存在');
        $member=Db::name('member')->find($id);
        if(empty($member))$this->error('会员不存在');
        if($member['isagent']==0)$this->success('取消成功');

        $result=Db::name('member')->where(array('id'=>$id))->update(array('isagent'=>0));
        if($result){
            user_log($this->mid,'cancelagent',1,'取消代理 ' .$id ,'manager');
            $this->success('取消成功');
            exit;
        }else{
            $this->error('取消失败');
        }
    }

    public function log($type='',$member_id=0){
        $model=Db::view('MemberLog','*')
            ->view('Member',['username'],'MemberLog.member_id=Member.id','LEFT');
        $where=array();
        if(!empty($type)){
            $where['action']=$type;
        }
        if($member_id!=0){
            $where['member_id']=$member_id;
        }

        $logs = $model->where($where)->paginate(15);
        $this->assign('logs', $logs);
        $this->assign('page',$logs->render());
        return $this->fetch();
    }
    public function logview(){
        $id=$this->request->get('id/d');
        $model=Db::name('MemberLog');

        $m=$model->where(["member_log.id"=>$id])->find();
        $member=Db::name('Member')->where(["id"=>$m['member_id']])->find();

        $this->assign('m', $m);
        $this->assign('member', $member);
        return $this->fetch();
    }

    public function logclear(){
        $date=$this->request->get('date');
        $d=strtotime($date);
        if(empty($d)){
            $date=date_sub(new \DateTime(date('Y-m-d')),new \DateInterval('P1M'));
            $d=$date->getTimestamp();
        }

        $model=Db::name('MemberLog');

        $model->where(array("create_time"=>array('ELT',$d)))->delete();

        user_log($this->mid,'clearmemberlog',1,'清除会员日志' ,'manager');
        $this->success("清除完成");
    }

    /**
     * 添加用户
     */
    public function add()
    {
        if ($this->request->isPost()) {
            //如果用户提交数据
            $data=$this->request->post();
            $validate=new MemberValidate();
            $validate->setId();
            if (!$validate->scene('register')->check($data)) {
                $this->error($validate->getError());
            } else {
                $data['salt']=random_str(8);
                $data['password']=encode_password($data['password'],$data['salt']);
                if ($member=MemberModel::create($data)) {
                    user_log($this->mid,'adduser',1,'添加会员'.$member->getLastInsID() ,'manager');
                    $this->success("用户添加成功", url('member/index'));
                } else {
                    $this->error("用户添加失败");
                }
            }
        }
        return $this->fetch();
    }
    /**
     * 更新会员信息
     */
    public function update($id)
    {
        $id=intval($id);
        if ($this->request->isPost()) {
            $data=$this->request->post();
            $validate=new MemberValidate();
            $validate->setId($id);
            if (!$validate->scene('edit')->check($data)) {
                $this->error($validate->getError());
            }else{
                if(!empty($data['password'])){
                    $data['salt']=random_str(8);
                    $data['password'] = encode_password($data['password'],$data['salt']);
                }else{
                    unset($data['password']);
                }

                //更新
                $member=MemberModel::get($id);
                if ($member->allowField(true)->save($data)) {
                    user_log($this->mid,'updateuser',1,'修改会员资料'.$id ,'manager');
                    $this->success("用户信息更新成功", url('member/index'));
                } else {
                    $this->error("未做任何修改,用户信息更新失败");
                }        
            }
        }
        $model = Db::name('Member')->find($id);
        $this->assign('model',$model);
        return $this->fetch();
    }
    /**
     * 删除管理员
     */
    public function delete($id)
    {
    	$id = intval($id);

        $model = Db::name('member');
        //查询status字段值
        $result = $model->find($id);
        //更新字段
        $data['id']=$id;
        if($result['status'] == 1){
        	$data['status']=0;
        }
        if($result['status'] == 0){
        	$data['status']=1;
        }
        if($model->update($data)){
            user_log($this->mid,'deleteuser',1,'禁用会员' ,'manager');
            $this->success("状态更新成功", url('member/index'));
        }else{
            $this->error("状态更新失败");
        }
    }
}
