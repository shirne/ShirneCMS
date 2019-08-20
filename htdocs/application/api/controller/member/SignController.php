<?php


namespace app\api\controller\member;


use app\api\Controller\AuthedController;
use app\common\model\MemberSignModel;

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
    
    public function dosign($mood=''){
        
        $result = $this->model->sign($this->user['id'],$mood,time(),true);
        if($result){
            $this->success('签到成功');
        }else{
            $this->error($this->model->getError());
        }
    }
    
    public function dosupsign($date, $mood=''){
        $result = $this->model->sign($this->user['id'],$mood,$date,true);
        if($result){
            $this->success('补签成功');
        }else{
            $this->error($this->model->getError());
        }
    }

    public function getlastsign()
    {
        $sign = $this->model->getLastSign($this->user['id']);
        return $this->response($sign);
    }
    
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
}