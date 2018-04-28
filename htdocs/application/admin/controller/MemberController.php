<?php
namespace app\admin\controller;
/**
 * 用户管理
 */
class MemberController extends BaseController
{
    public function _initialize()
    {
        parent::_initialize();

        M('manager')->where(array('id'=>$this->manage['id']))->save(array('last_view_member'=>time()));
    }

    /**
     * 用户列表
     */
    public function index()
    {
        $model = M('member');
        $where=array();
        $keyword=I('keyword');
        if(!empty($keyword)){
            $where['m.username|m.email|m.realname'] = array('like',"%$keyword%");
        }

        $referer=I('referer');
        if($referer!==''){
            if($referer!=='0'){
                $member=$model->where(array('id|username'=>$referer))->find();
                if(empty($member)){
                    $this->error('填写的会员不存在');
                }
                $where['m.referer'] = $member['id'];
            }else {
                $where['m.referer'] = intval($referer);
            }
        }

        $this->pagelist(
            $model->alias('m')->Join('__MEMBER__ rm ON m.referer=rm.id','LEFT'),
            $where,
            'm.id DESC','m.*,rm.username as refer_name,rm.realname as refer_realname,rm.isagent as refer_agent');
        $this->assign('referer',$referer);
        $this->assign('keyword',$keyword);
        $this->display();     
    }
    public function set_agent($id=0){
        if(empty($id))$this->error('会员不存在');
        $member=M('member')->find($id);
        if(empty($member))$this->error('会员不存在');

        $level=I('level/d',1);
        if($level>3)$level=3;

        if($member['isagent']==$level)$this->success('设置成功');

        $data=array('isagent'=>$level);
        if(empty($member['agentcode']))$data['agentcode']=random_str(6);
        $result=M('member')->where(array('id'=>$id))->save($data);
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
        $member=M('member')->find($id);
        if(empty($member))$this->error('会员不存在');
        if($member['isagent']==0)$this->success('取消成功');

        $result=M('member')->where(array('id'=>$id))->save(array('isagent'=>0));
        if($result){
            user_log($this->mid,'cancelagent',1,'取消代理 ' .$id ,'manager');
            $this->success('取消成功');
            exit;
        }else{
            $this->error('取消失败');
        }
    }

    public function log($type='',$member_id=0){
        $model=D('MemberLogView');
        $where=array();
        if(!empty($type)){
            $where['action']=$type;
        }
        if($member_id!=0){
            $where['member_id']=$member_id;
        }

        $this->pagelist($model,$where,'member_log.id DESC');
        $this->display();
    }
    public function logview(){
        $id=I('id/d');
        $model=D('MemberLogView');

        $m=$model->where("member_log.id= %d",$id)->find();

        $this->assign('m', $m);
        $this->display();
    }

    public function logclear(){
        $date=I('date');
        $d=strtotime($date);
        if(empty($d)){
            $date=date_sub(new \DateTime(date('Y-m-d')),new \DateInterval('P1M'));
            $d=$date->getTimestamp();
        }

        $model=M('MemberLog');

        $model->where(array("create_at"=>array('ELT',$d)))->delete();

        user_log($this->mid,'clearmemberlog',1,'清除会员日志' ,'manager');
        $this->success("清除完成");
    }

    /**
     * 添加用户
     */
    public function add()
    {
        //默认显示添加表单
        if (!IS_POST) {
            $this->display();
        }
        if (IS_POST) {
            //如果用户提交数据
            $model = D("Member");
            if (!$model->create()) {
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                $this->error($model->getError());
                exit();
            } else {
                $model->salt=random_str(8);
                $model->password=encode_password($model->password,$model->salt);
                if ($model->add()) {
                    user_log($this->mid,'adduser',1,'添加会员'.$model->getLastInsID() ,'manager');
                    $this->success("用户添加成功", U('member/index'));
                } else {
                    $this->error("用户添加失败");
                }
            }
        }
    }
    /**
     * 更新会员信息
     */
    public function update()
    {
        //默认显示添加表单
        if (!IS_POST) {
            $model = M('member')->find(I('id/d'));
            $this->assign('model',$model);
            $this->display();
        }
        if (IS_POST) {
            $model = D("Member");
            if (!$model->create()) {
                $this->error($model->getError());
            }else{
                //验证密码是否为空   
                $data = I();
                if(!empty($data['password'])){
                    $data['salt']=random_str(8);
                    $data['password'] = encode_password($data['password'],$data['salt']);
                }else{
                    unset($data['password']);
                }

                //更新
                if ($model->save($data)) {
                    user_log($this->mid,'updateuser',1,'修改会员资料'.$data['id'] ,'manager');
                    $this->success("用户信息更新成功", U('member/index'));
                } else {
                    $this->error("未做任何修改,用户信息更新失败");
                }        
            }
        }
    }
    /**
     * 删除管理员
     */
    public function delete($id)
    {
    	$id = intval($id);

        $model = M('member');
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
        if($model->save($data)){
            user_log($this->mid,'deleteuser',1,'禁用会员' ,'manager');
            $this->success("状态更新成功", U('member/index'));
        }else{
            $this->error("状态更新失败");
        }
    }
}
