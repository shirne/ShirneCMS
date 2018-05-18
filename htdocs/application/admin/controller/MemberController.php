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
    public function index($type=0)
    {
        $model = Db::view('__MEMBER__ m','*');
        $where=array();
        $keyword=$this->request->request('keyword');
        if(!empty($keyword)){
            $where[] = array('m.username|m.email|m.realname','like',"%$keyword%");
        }

        $referer=$this->request->request('referer');
        if(!empty($referer)){
            if($referer!='0'){
                $member=Db::name('Member')->where(array('id|username'=>$referer))->find();
                if(empty($member)){
                    $this->error('填写的会员不存在');
                }
                $where['m.referer'] = $member['id'];
            }else {
                $where['m.referer'] = intval($referer);
            }
        }
        if($type>0){
            $where['m.type'] = intval($type)-1;
        }

        $lists=$model->view('__MEMBER__ rm',['username'=> 'refer_name','realname'=> 'refer_realname','is_agent'=> 'refer_agent'],'m.referer=rm.id','LEFT')->where($where)->paginate(15);

        $this->assign('lists',$lists);
        $this->assign('types',getMemberTypes());
        $this->assign('levels',getMemberLevels());
        $this->assign('type',$type);
        $this->assign('page',$lists->render());
        $this->assign('referer',$referer);
        $this->assign('keyword',$keyword);
        return $this->fetch();
    }
    public function set_agent($id=0){
        if(empty($id))$this->error('会员不存在');
        $member=Db::name('member')->find($id);
        if(empty($member))$this->error('会员不存在');


        if($member['is_agent'])$this->success('设置成功');

        $result=MemberModel::setAgent($id);
        if($result){
            user_log($this->mid,'setagent',1,'设置代理 '.$id ,'manager');
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
        if($member['is_agent']==0)$this->success('取消成功');

        $result=MemberModel::cancelAgent($id);
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
                $member=MemberModel::create($data);
                if ($member->id) {
                    user_log($this->mid,'adduser',1,'添加会员'.$member->id ,'manager');
                    $this->success("用户添加成功", url('member/index'));
                } else {
                    $this->error("用户添加失败");
                }
            }
        }
        $model=array('type'=>1,'status'=>1);
        $this->assign('model',$model);
        $this->assign('types',getMemberTypes());
        return $this->fetch('update');
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
        $this->assign('types',getMemberTypes());
        $this->assign('model',$model);
        return $this->fetch();
    }
    public function recharge(){
        $id=$this->request->post('id/d');
        $amount=$this->request->post('amount');
        $reson=$this->request->post('reson');
        if(floatval($amount)!=$amount){
            $this->error('金额错误');
        }

        $logid=money_log($id,intval($amount*100),$reson,'system');
        if($logid){
            user_log($this->mid,'recharge',1,'会员充值 '.$id.','.$logid ,'manager');
            $this->success('充值成功');
        }else{
            $this->error('充值失败');
        }
    }
    /**
     * 删除会员
     */
    public function delete($id,$type=0)
    {
        $model = Db::name('member');
        $data['status']=$type==1?1:0;
        if($model->where('id','in',idArr($id))->update($data)){
            user_log($this->mid,$type==1?'enableuser':'disableuser',1,($type==1?'启用会员':'禁用会员').':'.$id ,'manager');
            $this->success("状态更新成功", url('member/index'));
        }else{
            $this->error("状态更新失败");
        }
    }

    /**
     * 会员注册情况统计
     * @param string $type
     * @return mixed
     */
    public function statics($type='date')
    {
        $format="'%Y-%m-%d'";

        $statics=Db::name('member')->field('count(id) as member_count,date_format(from_unixtime(create_time),' . $format . ') as awdate')->group('awdate')->select();

        $this->assign('statics',$statics);
        return $this->fetch();
    }
}
