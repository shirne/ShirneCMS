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
    
    public function getsigns(){
        $list = $this->model->getSigns($this->user['id']);
        return $this->response($list);
    }
}