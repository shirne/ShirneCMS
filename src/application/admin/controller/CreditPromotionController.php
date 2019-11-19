<?php

namespace app\admin\controller;


use app\admin\validate\CreditPromotionValidate;
use app\common\model\CreditPromotionModel;
use think\Db;

class CreditPromotionController extends BaseController
{
    /**
     * 会员级别列表
     */
    public function index()
    {
        $model = Db::name('creditPromotion');

        $lists=$model->order('sort ASC,id ASC')->select();
        $this->assign('lists',$lists);
        return $this->fetch();
    }


    /**
     * 添加等级
     */
    public function add()
    {
        if ($this->request->isPost()) {
            //如果用户提交数据
            $data=$this->request->post();
            $validate=new CreditPromotionValidate();
            $validate->setId();
            if (!$validate->check($data)) {
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                $this->error($validate->getError());
            } else {
                $Model=CreditPromotionModel::create($data);

                if ($Model['id']) {

                    user_log($this->mid,'addpromotion',1,'添加积分策略'.$Model['id'] ,'manager');
                    $this->success("添加成功", url('creditPromotion/index'));
                } else {
                    $this->error("添加失败");
                }
            }
        }
        $this->assign('model',[]);
        return $this->fetch('update');
    }
    /**
     * 更新会员组
     */
    public function update($id)
    {
        $id = intval($id);
        if ($this->request->isPost()) {
            $data=$this->request->post();
            $validate=new CreditPromotionValidate();
            $validate->setId($id);
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }else{
                $model=CreditPromotionModel::get($id);
                if ($model->allowField(true)->save($data)) {
                    user_log($this->mid,'updatepromotion',1,'修改积分策略'.$id ,'manager');
                    $this->success("更新成功", url('creditPromotion/index'));
                } else {
                    $this->error("更新失败");
                }
            }
        }
        $model = CreditPromotionModel::get($id);
        $this->assign('model',$model);
        return $this->fetch();
    }
    /**
     * 删除策略
     */
    public function delete($id)
    {
        $id = intval($id);
        
        $result = Db::name('creditPromotion')->delete($id);
        if($result){
            CreditPromotionModel::clearCache();
            $this->success("删除成功", url('creditPromotion/index'));
        }else{
            $this->error("删除失败");
        }
    }
}