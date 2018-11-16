<?php
namespace app\admin\controller;

use app\common\model\MemberModel;
use app\common\validate\MemberValidate;
use think\Db;

/**
 * 会员管理
 * Class MemberController
 * @package app\admin\controller
 */
class MemberController extends BaseController
{
    public function initialize()
    {
        parent::initialize();

        Db::name('Manager')->where('id',$this->manage['id'])->update(array('last_view_member'=>time()));
    }

    /**
     * 会员搜索接口
     * @param $key
     * @param int $type
     * @return \think\response\Json
     */
    public function search($key,$type=0){
        $model=Db::name('member')
            ->where('status',1);
        if(!empty($key)){
            $model->where('id|username|realname|mobile','like',"%$key%");
        }
        if(!empty($type)){
            $model->where('type',$type);
        }

        $lists=$model->field('id,username,realname,mobile,avatar,level_id,is_agent,gender,email,create_time')
            ->order('id ASC')->limit(10)->select();
        return json(['data'=>$lists,'status'=>1]);
    }

    /**
     * 会员列表
     * @param int $type
     * @param string $keyword
     * @param string $referer
     * @return mixed|\think\response\Redirect
     */
    public function index($type=0,$keyword='',$referer='')
    {
        if($this->request->isPost()){
            return redirect(url('',['referer'=>$referer,'type'=>$type,'keyword'=>base64_encode($keyword)]));
        }
        $keyword=empty($keyword)?"":base64_decode($keyword);
        $model = Db::view('__MEMBER__ m','*')
            ->view('__MEMBER__ rm',['username'=> 'refer_name','realname'=> 'refer_realname','is_agent'=> 'refer_agent'],'m.referer=rm.id','LEFT');
        if(!empty($keyword)){
            $model->whereLike('m.username|m.email|m.realname',"%$keyword%");
        }

        if($referer !== ''){
            if($referer!='0'){
                $member=Db::name('Member')->where('id|username',$referer)->find();
                if(empty($member)){
                    $this->error('填写的会员不存在');
                }
                $model->where('m.referer',$member['id']);
            }else {
                $model->where('m.referer',intval($referer));
            }
        }
        if($type>0){
            $model->where('m.type',intval($type)-1);
        }

        $lists=$model->order('m.id desc')->paginate(15);

        $this->assign('lists',$lists);
        $this->assign('moneyTypes',getMoneyFields(false));
        $this->assign('types',getMemberTypes());
        $this->assign('levels',getMemberLevels());
        $this->assign('type',$type);
        $this->assign('page',$lists->render());
        $this->assign('referer',$referer);
        $this->assign('keyword',$keyword);
        return $this->fetch();
    }

    /**
     * 设置代理
     * @param int $id
     */
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

    /**
     * 取消代理
     * @param int $id
     */
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

    /**
     * 余额记录
     * @param int $id
     * @param int $from_id
     * @param string $fromdate
     * @param string $todate
     * @param string $field
     * @param string $type
     * @return mixed
     */
    public function money_log($id=0,$from_id=0,$fromdate='',$todate='',$field='all',$type='all'){
        $model=Db::view('MemberMoneyLog mlog','*')
            ->view('Member m',['username','level_id','mobile'],'m.id=mlog.member_id','LEFT')
            ->view('Member fm',['username'=>'from_username','level_id'=>'from_level_id','mobile'=>'from_mobile'],'fm.id=mlog.member_id','LEFT');

        $levels=getMemberLevels();

        if($id>0){
            $model->where('mlog.member_id',$id);
            $this->assign('member',Db::name('member')->find($id));
        }
        if($from_id>0){
            $model->where('mlog.from_member_id',$from_id);
            $this->assign('from_member',Db::name('member')->find($from_id));
        }
        if(!empty($type) && $type!='all'){
            $model->where('mlog.type',$type);
        }else{
            $type='all';
        }
        if(!empty($field) && $field!='all'){
            $model->where('mlog.field',$field);
        }else{
            $field='all';
        }

        if(!empty($todate)){
            $totime=strtotime($todate.' 23:59:59');
            if($totime===false)$todate='';
        }
        if(!empty($fromdate)) {
            $fromtime = strtotime($fromdate);
            if ($fromtime === false) $fromdate = '';
        }
        if(!empty($fromtime)){
            if(!empty($totime)){
                $model->whereBetween('mlog.create_time',array($fromtime,$totime));
            }else{
                $model->where('mlog.create_time','EGT',$fromtime);
            }
        }else{
            if(!empty($totime)){
                $model->where('mlog.create_time','ELT',$totime);
            }
        }

        $logs = $model->order('ID DESC')->paginate(15);

        $types=getLogTypes();
        $fields=getMoneyFields();

        $stacrows=$model->group('mlog.field,mlog.type')->field('mlog.field,mlog.type,sum(mlog.amount) as total_amount')->select();
        $statics=[];
        foreach ($stacrows as $row){
            $statics[$row['field']][$row['type']]=$row['total_amount'];
        }
        foreach ($statics as $k=>$list){
            $statics[$k]['sum']=array_sum($statics[$k]);
        }

        $this->assign('id',$id);
        $this->assign('from_id',$from_id);
        $this->assign('fromdate',$fromdate);
        $this->assign('todate',$todate);
        $this->assign('type',$type);
        $this->assign('field',$field);

        $this->assign('types',$types);
        $this->assign('fields',$fields);
        $this->assign('levels',$levels);
        $this->assign('statics', $statics);
        $this->assign('logs', $logs);
        $this->assign('page',$logs->render());
        return $this->fetch();
    }

    /**
     * 会员日志
     * @param string $type
     * @param int $member_id
     * @return mixed
     */
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

        $logs = $model->where($where)->order('MemberLog.id DESC')->paginate(15);
        $this->assign('lists', $logs);
        $this->assign('page',$logs->render());
        return $this->fetch();
    }

    /**
     * 日志详情
     * @param $id
     * @return mixed
     */
    public function logview($id){
        $model=Db::name('MemberLog');

        $m=$model->find($id);
        $member=Db::name('Member')->find($m['member_id']);

        $this->assign('m', $m);
        $this->assign('member', $member);
        return $this->fetch();
    }

    /**
     * 清除日志
     */
    public function logclear(){
        $date=$this->request->get('date');
        $d=strtotime($date);
        if(empty($d)){
            $d=strtotime('-7days');
        }

        $model=Db::name('MemberLog');

        $model->where('create_time','ELT',$d)->delete();

        user_log($this->mid,'clearmemberlog',1,'清除会员日志' ,'manager');
        $this->success("清除完成");
    }

    /**
     * 添加
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
                    $this->success(lang('Add success!'), url('member/index'));
                } else {
                    $this->error(lang('Add failed!'));
                }
            }
        }
        $model=array('type'=>1,'status'=>1);
        $this->assign('model',$model);
        $this->assign('types',getMemberTypes());
        return $this->fetch('update');
    }

    /**
     * 修改
     * @param $id
     * @return mixed
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
                    $this->success(lang('Update success!'), url('member/index'));
                } else {
                    $this->error(lang('Update failed!'));
                }        
            }
        }
        $model = Db::name('Member')->find($id);
        $this->assign('types',getMemberTypes());
        $this->assign('model',$model);
        return $this->fetch();
    }

    /**
     * 充值
     */
    public function recharge(){
        $id=$this->request->post('id/d');
        $field=$this->request->post('field');
        $amount=$this->request->post('amount');
        $reson=$this->request->post('reson');
        if(floatval($amount)!=$amount){
            $this->error('金额错误');
        }
        if(!in_array($field,['money','credit'])){
            $this->error('充值类型错误');
        }

        $atext=$amount>0?'充值':'扣款';
        $logid=money_log($id,intval($amount*100),'系统扣款'.$atext.$reson,'system',$field);
        if($logid){
            user_log($this->mid,'recharge',1,'会员'.$atext.' '.$id.','.$amount ,'manager');
            $this->success($atext.'成功');
        }else{
            $this->error($atext.'失败');
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
            $this->success(lang('Update success!'), url('member/index'));
        }else{
            $this->error(lang('Update failed!'));
        }
    }

    /**
     * 会员注册情况统计
     * @param string $type
     * @param string $start_date
     * @param string $end_date
     * @return mixed
     */
    public function statics($type='date',$start_date='',$end_date='')
    {
        if($this->request->isPost()){
            if(!in_array($type,['date','month','year']))$type='date';
            return redirect(url('',['type'=>$type,'start_date'=>$start_date,'end_date'=>$end_date]));
        }
        $format="'%Y-%m-%d'";

        if($type=='month'){
            $format="'%Y-%m'";
        }elseif($type=='year'){
            $format="'%Y'";
        }

        $model=Db::name('member')->field('count(id) as member_count,date_format(from_unixtime(create_time),' . $format . ') as awdate');
        $start_date=format_date($start_date,'Y-m-d');
        $end_date=format_date($end_date,'Y-m-d');
        if(!empty($start_date)){
            if(!empty($end_date)){
                $model->whereBetween('create_time',[strtotime($start_date),strtotime($end_date.' 23:59:59')]);
            }else{
                $model->where('create_time','GT',strtotime($start_date));
            }
        }else{
            if(!empty($end_date)){
                $model->where('create_time','LT',strtotime($end_date.' 23:59:59'));
            }
        }

        $statics=$model->group('awdate')->select();

        $this->assign('statics',$statics);
        $this->assign('static_type',$type);
        $this->assign('start_date',$start_date);
        $this->assign('end_date',$end_date);
        return $this->fetch();
    }
}
