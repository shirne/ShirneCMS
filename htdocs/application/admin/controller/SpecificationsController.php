<?php

namespace app\admin\controller;


use app\admin\model\SpecificationsModel;
use app\admin\validate\SpecificationsValidate;
use think\Db;

/**
 * 商品规格管理
 * Class SpecificationsController
 * @package app\admin\controller
 */
class SpecificationsController extends BaseController
{
    /**
     * 列表
     */
    public function index()
    {
        if($this->request->isPost()){
            $data=$this->request->post();
            $data['id']=isset($data['id'])?intval($data['id']):0;
            $validate=new SpecificationsValidate();
            $validate->setId($data['id']);
            if(!$validate->check($data)){
                $this->error($validate->getError());
            }else{
                if($data['id']==0){
                    unset($data['id']);
                    $model=SpecificationsModel::create($data);
                    if($model['id']){
                        $this->success('添加成功');
                    }else{
                        $this->error('添加失败');
                    }
                }else{
                    SpecificationsModel::update($data);
                    $this->success('保存成功');
                }
            }

        }
        $model = new SpecificationsModel();
        $lists=$model->order('ID ASC')->select();
        $this->assign('lists',$lists);
        return $this->fetch();
    }

    /**
     * 删除
     */
    public function delete($id)
    {
        $id = intval($id);
        $model = Db::name('Specifications');
        $result = $model->delete($id);
        if($result){
            $this->success("删除成功", url('specifications/index'));
        }else{
            $this->error("删除失败");
        }
    }
}