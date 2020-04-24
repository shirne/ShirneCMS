<?php

namespace app\admin\controller;


use app\common\model\MemberCashinModel;
use app\common\model\MemberOauthModel;
use app\common\model\MemberRechargeModel;
use app\common\model\PayOrderModel;
use app\common\model\WechatModel;
use EasyWeChat\Factory;
use shirne\excel\Excel;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use think\facade\Db;
use think\facade\Log;

/**
 * 充值提现管理
 * Class PaylogController
 * @package app\admin\controller
 */
class PaylogController extends BaseController
{
    public function index($id=0,$fromdate='',$todate='',$ordertype='all',$type='all', $showall = 0){
        $model=Db::view('payOrder po','*')
        ->view('Member m',['username','nickname','avatar','level_id','mobile'],'m.id=po.member_id','LEFT');

        $levels=getMemberLevels();

        if($id>0){
            $model->where('po.member_id',$id);
            $this->assign('member',Db::name('member')->find($id));
        }
        if(!$showall){
            $model->where('po.status','>',0);
        }
        
        if(!empty($type) && $type!='all'){
            $model->where('po.pay_type',$type);
        }else{
            $type='all';
        }
        if(!empty($ordertype) && $ordertype!='all'){
            $model->where('po.order_type',$ordertype);
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
                $model->whereBetween('po.create_time',array($fromtime,$totime));
            }else{
                $model->where('po.create_time','>=',$fromtime);
            }
        }else{
            if(!empty($totime)){
                $model->where('po.create_time','<=',$totime);
            }
        }

        $logs = $model->order('ID DESC')->paginate(15);

        $all = ['all'=> '全部'];
        $orderTypes=array_merge($all, PayOrderModel::$orderTypes);
        $payTypes=array_merge($all, PayOrderModel::$payTypes);

        $orderDetails=[
            'order'=>'order/detail',
            'credit'=>'creditOrder/detail',
            'groupbuy'=>'groupbuy.order/detail'
        ];

        $stacrows=$model->group('po.order_type,po.pay_type')->setOption('field',[])->setOption('order','po.order_type')->field('po.order_type,po.pay_type,sum(po.pay_amount) as total_amount')->select();
        $statics=[];
        foreach ($stacrows as $row){
            $statics[$row['pay_type']][$row['order_type']]=$row['total_amount'];
        }
        foreach ($statics as $k=>$list){
            $statics[$k]['sum']=array_sum($list);
        }

        $this->assign('id',$id);
        $this->assign('fromdate',$fromdate);
        $this->assign('todate',$todate);
        $this->assign('type',$type);
        $this->assign('ordertype',$ordertype);

        $this->assign('orderTypes',$orderTypes);
        $this->assign('orderDetails',$orderDetails);
        $this->assign('payTypes',$payTypes);
        $this->assign('levels',$levels);
        $this->assign('statics', $statics);
        $this->assign('logs', $logs);
        $this->assign('page',$logs->render());
        return $this->fetch();
    }
    
    /**
     * 充值管理
     * @param string $key
     * @param int $status
     * @return mixed
     */
    public function recharge($key='',$status=0){
        $model=Db::view('__MEMBER_RECHARGE__ mr','*');
        $where=array();
        if($status>0){
            switch ($status){
                case 1:
                    $where['mr.status']=0;
                    $where['mr.remark']='';
                    break;
                case 2:
                    $where['mr.status']=0;
                    $where['mr.remark']=array('NEQ','');
                    break;
                case 3:
                    $where['mr.status']=1;
                    break;
                case 4:
                    $where['mr.status']=2;
                    break;
                default:
                    $status=9;
            }
        }
        if(!empty($key)){
            $where[]=array('m.username','LIKE',"%$key%");
        }

        $lists=$model->view('__MEMBER__ m',['username','realname'],'mr.member_id=m.id','LEFT')
            ->view('__PAYTYPE__ p',['type','cardname','bank','cardno'],'mr.paytype_id=p.id','LEFT')
            ->order('mr.id DESC')->paginate(15);

        $this->assign('lists',$lists);
        $this->assign('page',$lists->render());
        $paytype=getPaytypes();

        $total=Db::name('MemberRecharge')->where('status',1)->sum('amount');
        $this->assign('total',$total);
        $this->assign('keyword',$key);
        $this->assign('status',$status);
        $this->assign('paytype',$paytype);
        return $this->fetch();
    }

    public function rechargeView($id){
        $id=intval($id);
        $model=Db::name('MemberRecharge')->find($id);
        $member=Db::name('member')->where('id',$model['member_id'])->find();

        $paytype=Db::name('member')->where('id',$model['paytype_id'])->find();

        $this->assign('model',$model);
        $this->assign('member',$member);
        $this->assign('paytype',$paytype);
        return $this->fetch();
    }

    /**
     * 充值成功
     * @param string $id
     */
    public function rechargeupdate($id=''){
        $id=intval($id);
        if($id==0)$this->error('参数错误 ');

        /**
         * @var $recharge MemberRechargeModel
         */
        $recharge=MemberRechargeModel::find($id);
        if(empty($recharge))$this->error('充值单不存在');
        if($recharge['status']!=0)$this->error('充值单已处理过了');


        $data['status']=1;
        $data['audit_time']=time();

        $recharge->updateStatus($data);
        Db::name('member')->where('id',$recharge['member_id'])->inc('total_recharge',$recharge['amount']);
        user_log($this->mid,'rechargeaudit',1,'审核充值单 '.$id ,'manager');
        $this->success('处理成功！');
    }

    /**
     * 充值撤销
     * @param string $id
     */
    public function rechargecancel($id=''){
        $id=intval($id);
        if($id==0)$this->error('参数错误 ');
        $recharge=Db::name('member_recharge')->find($id);
        if(empty($recharge))$this->error('充值单不存在');
        if($recharge['status']!=1)$this->error('充值单未成功');

        $recharge=Db::name('member_recharge')->lock(true)->find($id);
        if($recharge['status']!=1)$this->error('充值单未成功');

        $loged=money_log($recharge['member_id'],-$recharge['amount'],'充值撤销','charge');
        if($loged){
            $data=array();
            $data['status']=0;
            $data['audit_time']=time();
            Db::name('member_recharge')->where('id',$recharge['id'])->update($data);
            Db::name('member')->where('id',$recharge['member_id'])->dec('total_recharge',$recharge['amount']);
            user_log($this->mid,'rechargecancel',1,'撤销充值单 '.$id ,'manager');
            $this->success('处理成功！');
        }else{
            $this->error('处理失败！');
        }
    }

    /**
     * 充值作废
     * @param string $id
     */
    public function rechargedelete($id=''){
        $id=intval($id);
        if($id==0)$this->error('参数错误 ');
        $recharge=Db::name('member_recharge')->find($id);
        if(empty($recharge))$this->error('充值单不存在');
        if($recharge['status']!=0)$this->error('充值单已处理过了');

        $recharge=Db::name('member_recharge')->lock(true)->find($id);
        if($recharge['status']!=0)$this->error('充值单已处理过了');
        $data=array();
        $data['status']=2;
        $data['audit_time']=time();
        Db::name('member_recharge')->where('id',$recharge['id'])->update($data);
        user_log($this->mid,'rechargedelete',1,'作废充值单 '.$id ,'manager');
        $this->success('处理成功！');
    }

    /**
     * 提现管理
     * @param string $key
     * @param string $status
     * @return mixed|\think\response\Redirect
     */
    public function cashin($key='',$status=''){
        if($this->request->isPost()){
            return redirect(url('',['status'=>$status,'key'=>base64_encode($key)]));
        }
        $key=empty($key)?'':base64_decode($key);
        $model=Db::view('__MEMBER_CASHIN__ mc','*')->view('__MEMBER__ m',['username','nickname','avatar','realname'],'mc.member_id=m.id','LEFT');
        
        if(!empty($key)){
            $model->where('m.username|m.nickname|m.realname|mc.card_name','LIKE',"%$key%");
        }
        if($status!==''){
            $model->where('m.status',$status);
        }

        $lists=$model->order('mc.id DESC')->paginate(15);


        $this->assign('lists',$lists);
        $this->assign('page',$lists->render());
        $total=Db::name('MemberCashin')->where('status','>',0)->sum('amount');
        $this->assign('total',$total);
        $this->assign('status',$status);
        $this->assign('keyword',$key);
        return $this->fetch();
    }

    /**
     * 导出提现记录
     * @param string $ids
     * @param string $status
     * @param string $key
     */
    public function export($ids='',$status='',$key=''){
        $model=Db::view('__MEMBER_CASHIN__ mc','*')->view('__MEMBER__ m',['username','realname'],'mc.member_id=m.id','LEFT');
        if(empty($ids)){
            if(!empty($key)){
                $key=base64_decode($key);
                $model->where('m.username|mc.card_name','LIKE',"%$key%");
            }
            if($status!==''){
                $model->where('m.status',$status);
            }
        }elseif($ids=='status'){
            $model->where('mc.status',0);
        }else{
            $model->whereIn('mc.id',idArr($ids));
        }

        $rows=$model->order('mc.id DESC')->select();
        if(empty($rows)){
            $this->error('没有选择要导出的项目');
        }

        $excel=new Excel();
        $excel->setHeader(array(
            '编号','会员ID','会员账号',
            '提现来源','提现金额','应转款','申请时间',
            '提现方式','银行','分行','开户名','卡号','状态'
        ));
        $excel->setColumnType('A',DataType::TYPE_STRING);
        $excel->setColumnType('B',DataType::TYPE_STRING);
        $excel->setColumnType('E',DataType::TYPE_STRING);
        $excel->setColumnType('F',DataType::TYPE_STRING);
        $excel->setColumnType('L',DataType::TYPE_STRING);

        foreach ($rows as $row){
            $excel->addRow(array(
                $row['id'],$row['member_id'],$row['username'],
                money_type($row['from_field'],false),showmoney($row['amount']),showmoney($row['real_amount']),
                date('Y-m-d H:i:s',$row['create_time']),
                showcashtype($row['cashtype']),
                $row['bank_name'],$row['bank'],$row['card_name'],$row['cardno'],
                audit_status($row['status'],false)
            ));
        }

        $excel->output(date('Y-m-d-H-i').'-提现单导出['.count($rows).'条]');
    }

    /**
     * 提现成功
     * @param string $id
     */
    public function cashupdate($id='',$paytype=''){
        $id=intval($id);
        if($id==0)$this->error('参数错误 ');
        $cash=MemberCashinModel::find($id);
        if(empty($cash))$this->error('提现单不存在');
        if($cash['status']!=0)$this->error('提现单已处理过了');

        $successed=true;
        if($paytype=='wechat'){
            $successed=false;
            if($cash['cashtype']=='wechat'){
                $wechats=MemberOauthModel::getAccountsByMemberAndType($cash['member_id']);
            }else{
                $wechats=WechatModel::where('account_type','service')->select();
            }
            if(empty($wechats)){
                $this->error('未查询到微信服务号资料,请先设置资料');
            }
            
            foreach($wechats as $wechat){
                if(empty($wechat['cert_path']) || empty($wechat['key_path']))continue;
                $payment=Factory::payment(WechatModel::to_pay_config($wechat,'',true));
                $paydata=[
                    'partner_trade_no' => 'CASH'.$cash['id'], // 商户订单号，需保持唯一性(只能是字母或者数字，不能包含有符号)
                    'openid' =>  $wechat['openid'],
                    'check_name' => 'NO_CHECK', // NO_CHECK：不校验真实姓名, FORCE_CHECK：强校验真实姓名
                    //'re_user_name' => $cash['card_name'], // 如果 check_name 设置为FORCE_CHECK，则必填用户真实姓名
                    'amount' => $cash['real_amount'], // 企业付款金额，单位为分
                    'desc' => '提现', // 企业付款操作说明信息。必填
                ];
                if($cash['cashtype']=='wechat'){
                    $paydata['openid']=$wechat['openid'];
                    $paydata['check_name']='FORCE_CHECK';
                    $paydata['re_user_name']=$cash['card_name'];
                }else{
                    $paydata['enc_bank_no']=$cash['cardno'];
                    $paydata['enc_true_name']=$cash['card_name'];
                    $paydata['bank_code']=$this->getBankCode($cash['bank']);
                }

                $result = $payment->transfer->toBalance($paydata);
                break;
            }
            if(empty($result)){
                $this->error('微信服务号配置不完整,请先设置资料');
            }
        }elseif($paytype=='wechatpack'){
            $successed=false;
            $wechats=MemberOauthModel::getAccountsByMemberAndType($cash['member_id']);
            foreach($wechats as $wechat){
                if(empty($wechat['cert_path']) || empty(empty($wechat['key_path'])))continue;
                $payment=Factory::payment(WechatModel::to_pay_config($wechat,'',true));
                $redpackData = [
                    'mch_billno'   => 'CASH'.$cash['id'],
                    'send_name'    => getSetting('site-name'),
                    're_openid'    => $wechat['openid'],
                    'total_num'    => 1,  //固定为1，可不传
                    'total_amount' => $cash['real_amount'],  //单位为分，不小于100
                    'wishing'      => '恭喜发财',
                    //'client_ip'    => '192.168.0.1',  //可不传，不传则由 SDK 取当前客户端 IP
                    'act_name'     => '提现',
                    'remark'       => '',
                    // ...
                ];
                $result = $payment->redpack->sendNormal($redpackData);
                break;
            }
            if(empty($result)){
                $this->error('小程序配置不完整,请先设置资料');
            }
        }elseif($paytype=='wechatminipack'){
            $successed=false;
            $wechats=MemberOauthModel::getAccountsByMemberAndType($cash['member_id'],'miniprogram');
            foreach($wechats as $wechat){
                
                if(!empty($cash['appid']) && $wechat['appid']==$cash['appid']){
                    if(empty($wechat['cert_path']) || empty(empty($wechat['key_path']))){
                        $this->error('小程序支付信息配置错误');
                    }
                    //$payment=Factory::payment(WechatModel::to_pay_config($wechat));

                    $this->error('暂不支持小程序红包');
                    break;
                }
            }
        }else{
            $paytype='handle';
        }
        Log::record('提现:'.var_export($result,true));
        if(!empty($result)){
            if($result['return_code']!='SUCCESS'){
                $this->error($result['return_msg']);
            }
            if($result['result_code']=='SUCCESS'){
                $successed=true;
            }else{
                $this->error($result['err_code'].':'.$result['err_code_des']);
            }
        }
        if(!$successed){
            $this->error('支付信息配置错误');
        }
        

        $data=array();
        $data['paytype']=$paytype;
        $data['status']=1;
        $data['audit_time']=time();
        $cash->updateStatus($data);
        
        user_log($this->mid,'cashaudit',1,'处理提现单 '.$id ,'manager');
        $this->success('处理成功！');
    }

    private function getBankCode($bankname)
    {
        $banklist=banklist(true);
        return isset($banklist[$bankname])?$banklist[$bankname]:'';
    }

    /**
     * 提现驳回
     * @param string $id
     */
    public function cashdelete($id=''){
        $id=intval($id);
        if($id==0)$this->error('参数错误 ');
        $cash=MemberCashinModel::find($id);
        if(empty($cash))$this->error('提现单不存在');
        
        if($cash['status']!=0)$this->error('提现单已处理过了');

        $data['status']=-1;
        $cash->updateStatus($data);

        user_log($this->mid,'cashdelete',1,'驳回提现单 '.$id ,'manager');
        $this->success('处理成功！');
    }
}