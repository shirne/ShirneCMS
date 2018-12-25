<?php

namespace app\index\controller\member;

use app\common\validate\MemberCardValidate;
use think\Db;

/**
 * 账户控制器
 * Class AccountController
 * @package app\index\controller\member
 */
class AccountController extends BaseController
{
    public function cards(){
        $cards=Db::name('MemberCard')->where('member_id',$this->userid)->select();

        $this->assign('cards',$cards);
        return $this->fetch();
    }
    public function cardEdit($id=0){
        if($id>0) {
            $card = Db::name('MemberCard')->where('id' , $id)
                ->where('member_id',$this->userid)->find();
        }else{
            $card=array();
        }
        if($this->request->isPost()){
            $card=$this->request->only('cardno,bankname,cardname,bank,is_default','post');
            $card['is_default']=empty($card['is_default'])?0:1;
            $validate=new MemberCardValidate();

            if(!$validate->check($card)){
                $this->error($validate->getError());
            }else {
                if ($id > 0) {
                    Db::name('MemberCard')->where('id' , $id)->update($card);
                } else {
                    $card['member_id'] = $this->userid;
                    $id = Db::name('MemberCard')->insert($card,false,true);
                }
                if ($card['is_default']) {
                    Db::name('MemberCard')->where('id' , 'NEQ', $id)
                        ->where( 'member_id' , $this->userid)
                        ->update(array('is_default' => 0));
                }
                $this->success('保存成功',aurl('index/member.account/cards'));
            }
        }

        $this->assign('card',$card);
        $this->assign('banklist',banklist());
        return $this->fetch();
    }
    public function cashList(){
        $model=Db::name('memberCashin')->where('member_id',$this->userid);

        $cashes = $model->paginate(15);

        $this->assign('page',$cashes->render());
        $this->assign('cashes',$cashes);
        return $this->fetch();
    }
    public function cash(){
        $hascash=Db::name('memberCashin')->where(array('member_id'=>$this->userid,'status'=>0))->count();
        if($hascash>0){
            $this->error('您有提现申请正在处理中',aurl('index/member/index'));
        }
        $cards=Db::name('MemberCard')->where('member_id',$this->userid)->select();
        if($this->request->isPost()){
            $amount=$_POST['amount']*100;
            $bank_id=intval($_POST['card_id']);
            $card=Db::name('MemberCard')->where(array('member_id'=>$this->userid,'id'=>$bank_id))->find();
            $data=array(
                'member_id'=>$this->userid,
                'amount'=>$amount,
                'real_amount'=>$amount-$amount*$this->config['cash_fee']*.01,
                'create_at'=>time(),
                'bank_id'=>$bank_id,
                'bank'=>$card['bank'],
                'bank_name'=>$card['bankname'],
                'card_name'=>$card['cardname'],
                'cardno'=>$card['cardno'],
                'status'=>0,
                'remark'=>$_POST['remark']
            );
            if(empty($data['amount']) || $data['amount']<$this->config['cash_limit']){
                $this->error('提现金额填写错误');
            }
            if($this->config['cash_power']>0 && $data['amount']%$this->config['cash_power']>0){
                $this->error('提现金额必需是'.$this->config['cash_power'].'的倍数');
            }
            if($data['amount']>$this->user['money']){
                $this->error('可提现金额不足');
            }
            $addid=Db::name('memberCashin')->insert($data);
            if($addid) {
                money_log($this->userid,-$data['amount'],'提现申请扣除','cash');
                $this->success('提现申请已提交',aurl('index/member/index'));
            }else{
                $this->error('申请失败');
            }
        }
        $this->assign('cards',$cards);
        $this->assign('banklist',banklist());
        return $this->fetch();
    }

    public function moneyLog($type='',$field=''){
        $model=Db::view('MemberMoneyLog mlog','*')
            ->view('Member m',['username','level_id'],'m.id=mlog.from_member_id','LEFT')
            ->where('mlog.member_id',$this->userid);
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

        $logs = $model->order('mlog.id DESC')->paginate(10);

        $types=getLogTypes();
        $fields=getMoneyFields();
        $this->assign('type',$type);
        $this->assign('types',$types);
        $this->assign('field',$field);
        $this->assign('fields',$fields);
        $this->assign('page',$logs->render());
        $this->assign('logs',$logs);
        return $this->fetch();
    }

    /**
     * 会员转账
     */
    public function transfer(){
        if($this->request->isPost()){
            $secpassword=$this->request->post('secpassword');
            if(empty($secpassword)){
                $this->error('请填写安全密码');
            }
            if(!compare_secpassword($this->user,$secpassword)){
                $this->error('安全密码错误');
            }
            $data=$this->request->only('action,field,member_id,amount','post');
            $data['amount']=floatval($data['amount']);
            if($data['action']=='transout'){
                if(!in_array($data['field'],['money','credit','awards'])){
                    $this->error('转赠积分类型错误');
                }
                $tomember=Db::name('member')->where('id|username|mobile',$data['member_id'])->find();
                if(empty($tomember)){
                    $this->error('会员信息错误');
                }
                if($data['amount']<=0){
                    $this->error('转赠金额错误');
                }
                if($data['amount']*100>$this->user[$data['field']]){
                    $this->error('您的余额不足');
                }
                money_log($this->userid,-$data['amount']*100,'转赠给会员'.$tomember['username'],'transout',$tomember['id'],$data['field']);
                money_log($tomember['id'],$data['amount']*100,'会员'.$this->user['username'].'转入','transin',$this->userid,'money');
                if($data['field']=='credit'){
                    $this->unfreeze($data['amount']);
                }
                $this->success('转赠成功');
            }elseif($data['action']=='transmoney'){
                if(!in_array($data['field'],['credit','awards'])){
                    $this->error('转入积分类型错误');
                }
                if($data['amount']<=0){
                    $this->error('转入金额错误');
                }
                if($data['amount']*100>$this->user[$data['field']]){
                    $this->error('您的积分不足');
                }
                money_log($this->userid,-$data['amount']*100,'转入消费积分','transout',$this->userid,$data['field']);
                money_log($this->userid,$data['amount']*100,'从'.money_type($data['field'],false).'转入','transin',$this->userid,'money');
                if($data['field']=='credit'){
                    $this->unfreeze($data['amount']);
                }
                $this->success('转入成功');
            }
        }
        $this->error('非法操作');
    }

    private function unfreeze($amount){
        $amount=$amount*100;
        $freezes=Db::name('memberFreeze')->where('member_id',$this->userid)
            ->where('status',1)->order('freeze_time ASC,amount ASC,id ASC')->select();
        $unfreezed=0;
        foreach ($freezes as $freeze){
            Db::name('memberFreeze')->where('id',$freeze['id'])->update(['status'=>0]);
            $unfreezed += $freeze['amount'];
            if($unfreezed>=$amount){
                if($unfreezed>$amount){
                    $toFreeze=$unfreezed-$amount;
                    $newData=[
                        'member_id'=>$this->userid,
                        'award_log_id'=>$freeze['award_log_id'],
                        'amount'=>$toFreeze,
                        'create_time'=>$freeze['create_time'],
                        'freeze_time'=>$freeze['freeze_time'],
                        'status'=>1
                    ];
                    Db::name('memberFreeze')->insert($newData);
                }
                break;
            }
        }
        return true;
    }
}