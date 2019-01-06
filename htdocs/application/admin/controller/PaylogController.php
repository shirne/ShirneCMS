<?php

namespace app\admin\controller;


use app\common\model\MemberRechargeModel;
use excel\Excel;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use think\Db;

/**
 * 充值提现管理
 * Class PaylogController
 * @package app\admin\controller
 */
class PaylogController extends BaseController
{
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
        $model=Db::view('__MEMBER_CASHIN__ mc','*')->view('__MEMBER__ m',['username','realname'],'mc.member_id=m.id','LEFT');
        $where=array();
        if(!empty($key)){
            $model->where('m.username|mc.card_name','LIKE',"%$key%");
        }
        if($status!==''){
            $model->where('m.status',$status);
        }

        $lists=$model->order('mc.id DESC')->paginate(15);


        $this->assign('lists',$lists);
        $this->assign('page',$lists->render());
        $total=Db::name('MemberCashin')->where('status',1)->sum('amount');
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
    public function cashupdate($id=''){
        $id=intval($id);
        if($id==0)$this->error('参数错误 ');
        $recharge=Db::name('member_cashin')->find($id);
        if(empty($recharge))$this->error('提现单不存在');
        if($recharge['status']!=0)$this->error('提现单已处理过了');

        $recharge=Db::name('member_cashin')->lock(true)->find($id);
        if($recharge['status']!=0)$this->error('提现单已处理过了');
        $data=array();
        $data['status']=1;
        $data['audit_time']=time();
        Db::name('member_cashin')->where('id',$recharge['id'])->update($data);
        user_log($this->mid,'cashaudit',1,'处理提现单 '.$id ,'manager');
        $this->success('处理成功！');
    }

    /**
     * 提现驳回
     * @param string $id
     */
    public function cashdelete($id=''){
        $id=intval($id);
        if($id==0)$this->error('参数错误 ');
        $cash=Db::name('member_cashin')->find($id);
        if(empty($cash))$this->error('提现单不存在');
        if($cash['status']!=0)$this->error('提现单已处理过了');

        $cash=Db::name('member_cashin')->lock(true)->find($id);
        if($cash['status']!=0)$this->error('提现单已处理过了');
        $data['status']=2;
        Db::name('member_cashin')->where('id',$cash['id'])->update($data);

        money_log($cash['member_id'],$cash['amount'],'提现驳回','cash');

        user_log($this->mid,'cashdelete',1,'驳回提现单 '.$id ,'manager');
        $this->success('处理成功！');
    }
}