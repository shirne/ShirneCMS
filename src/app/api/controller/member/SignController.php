<?php


namespace app\api\controller\member;


use app\api\controller\AuthedController;
use app\common\model\MemberSignModel;
use think\facade\Db;

/**
 * 签到管理
 * @package app\api\controller\member
 */
class SignController extends AuthedController
{
    /**
     * @var MemberSignModel
     */
    protected $model;
    public function initialize()
    {
        parent::initialize();
        $this->model = MemberSignModel::getInstance();
    }
    
    /**
     * 签到
     * @param string $mood 心情
     * @return void 
     */
    public function dosign($mood=''){
        
        $result = $this->model->sign($this->user['id'],$mood,time());
        if($result){
            $msg = $this->model->getError();
            $this->success($msg?:'签到成功');
        }else{
            $this->error($this->model->getError());
        }
    }
    
    /**
     * 补签
     * @param mixed $date 
     * @param string $mood 
     * @return void 
     */
    public function dosupsign($date, $mood=''){
        $result = $this->model->sign($this->user['id'],$mood,$date,true);
        if($result){
            $this->success('补签成功');
        }else{
            $this->error($this->model->getError());
        }
    }

    /**
     * 获取最后一次签到
     * @return Json 
     */
    public function getlastsign()
    {
        $sign = $this->model->getLastSign($this->user['id']);
        return $this->response($sign);
    }
    
    /**
     * 获取签到记录
     * @param mixed|null $from_date 
     * @param mixed|null $to_date 
     * @return Json 
     */
    public function getsigns($from_date=NULL, $to_date=NULL){
        $dates=time();
        if(!is_null($from_date)){
            $dates=[$from_date];
            if(!is_null($to_date)){
                $dates[]=$to_date;
            }
        }
        $list = $this->model->getSigns($this->user['id'],$dates);
        return $this->response($list);
    }
    
    /**
     * 签到总天数
     * @param string $fromdate 
     * @return Json 
     */
    public function totaldays($fromdate='')
    {
        $model = Db::name('signLog')->where('member_id',$this->user['id']);
        if(!empty($fromdate)){
            $fromtime = strtotime($fromdate);
            $model->where('signdate','>=', date('Y-m-d', $fromtime));
        }
        $total = $model->count();
        return $this->response(intval($total));
    }

    /**
     * 签到得到的总积分
     * @param string $fromdate 
     * @return Json 
     */
    public function totalcredit($fromdate='')
    {
        $model = Db::name('memberMoneyLog')->where('member_id',$this->user['id'])->where('type','sign');
        if(!empty($fromdate)){
            $fromtime = strtotime($fromdate);
            $model->where('create_time','>=', $fromtime);
        }
        $total = $model->sum('amount');
        return $this->response(intval($total*.01));
    }
}