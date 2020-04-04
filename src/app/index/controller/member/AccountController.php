<?php

namespace app\index\controller\member;

use app\common\validate\MemberCardValidate;
use think\facade\Db;

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
            $card=$this->request->post(['cardno','bankname','cardname','bank','is_default']);
            $card['is_default']=empty($card['is_default'])?0:1;
            $validate=new MemberCardValidate();

            if(!$validate->check($card)){
                $this->error($validate->getError());
            }else {
                if ($id > 0) {
                    Db::name('MemberCard')->where('id' , $id)->update($card);
                } else {
                    $card['member_id'] = $this->userid;
                    $id = Db::name('MemberCard')->insert($card,true);
                }
                if ($card['is_default']) {
                    Db::name('MemberCard')->where('id' , '<>', $id)
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

    /**
     * 充值
     * @return mixed
     */
    public function recharge(){
        $hasRecharge=Db::name('memberRecharge')->where('status',0)
            ->where('member_id',$this->userid)->find();
        if($this->request->isPost()){
            if($hasRecharge>0){
                $this->error('您有充值申请正在处理中',url('index/member.account/rechargeList'));
            }
            $amount=$_POST['amount']*100;
            $type=$_POST['type_id'];
            $pay_bill='';
            if($type=='wechat'){
                $typeid = -1;
            }else {

                $typeid = intval($_POST['type_id']);
                $paytype = Db::name('paytype')->where('status', 1)->where('id', $typeid)->find();
                if (empty($paytype)) {
                    $this->error('充值方式错误');
                }

                $uploaded = $this->upload('recharge', 'pay_bill');
                if (!$uploaded) {
                    $this->error($this->uploadError);
                }
                $pay_bill=$uploaded['url'];
            }

            $data=array(
                'member_id'=>$this->userid,
                'amount'=>$amount,
                'paytype_id'=>$typeid,
                'pay_bill'=>$pay_bill,
                'create_time'=>time(),
                'status'=>0,
                'remark'=>$_POST['remark']
            );
            if(empty($data['amount']) || $data['amount']<$this->config['recharge_limit']){
                $this->error('充值金额填写错误');
            }
            if($this->config['recharge_power']>0 && $data['amount']%$this->config['recharge_power']>0){
                $this->error('充值金额必需是'.$this->config['recharge_power'].'的倍数');
            }

            $addid=Db::name('memberRecharge')->insert($data,true);
            if($addid) {
                if($type=='wechat'){
                    $this->success('充值订单提交成功，即将跳转到支付页面', url('index/order/wechatpay',['order_id'=>'CZ_'.$addid]));
                }else {
                    $this->success('充值申请已提交', url('index/member.account/rechargeList'));
                }
            }else{
                $this->error('提交失败');
            }
        }
        $types=Db::name('paytype')->where('status',1)->order('id ASC')->select();
        $this->assign('types',$types);
        $this->assign('hasRecharge',empty($hasRecharge)?0:1);
        return $this->fetch();
    }

    public function rechargeList(){
        $model=Db::name('memberRecharge')->where('member_id',$this->userid);

        $recharges = $model->order('id DESC')->paginate(15);

        $this->assign('page',$recharges->render());
        $this->assign('recharges',$recharges);
        return $this->fetch();
    }

    public function recharge_cancel($order_id){
        $result=Db::name('memberRecharge')->where('id',$order_id)->update(['status'=>2]);
        if($result){
            $this->success('取消成功');
        }else{
            $this->error('取消失败');
        }
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
            $this->error('您有提现申请正在处理中',aurl('index/member.account/cashList'));
        }
        $cards=Db::name('MemberCard')->where('member_id',$this->userid)->select();
        if($this->request->isPost()){
            $amount=$this->request->post('amount')*100;
            $bank_id=$this->request->post('card_id/d');
            if(empty($bank_id)){
                $carddata=$this->request->post(['bank','bankname','cardname','cardno']);
                if(empty($carddata['bank'])){
                    $this->error('请填写银行名称');
                }
                if(empty($carddata['bankname'])){
                    $this->error('请填写开户行名称');
                }
                if(empty($carddata['cardname'])){
                    $this->error('请填写开户名称');
                }
                if(empty($carddata['cardno'])){
                    $this->error('请填写卡号');
                }
                $carddata['member_id']=$this->userid;
                $bank_id=Db::name('MemberCard')->insert($carddata,true);
            }
            $card=Db::name('MemberCard')->where(array('member_id'=>$this->userid,'id'=>$bank_id))->find();
            $data=array(
                'member_id'=>$this->userid,
                'amount'=>$amount,
                'real_amount'=>$amount-$amount*$this->config['cash_fee']*.01,
                'create_time'=>time(),
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
            if($data['amount']>$this->user['reward']){
                $this->error('可提现金额不足');
            }
            $addid=Db::name('memberCashin')->insert($data);
            if($addid) {
                money_log($this->userid,-$data['amount'],'提现申请扣除','cash',0,'reward');
                Db::name('member')->where('id',$this->userid)->inc('froze_money',$data['amount']);
                $this->success('提现申请已提交',aurl('index/member.account/cashList'));
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