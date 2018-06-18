<?php

namespace app\admin\controller;

use app\admin\validate\WechatValidate;
use think\Db;

/**
 * Class WechatController
 * @package app\admin\controller
 */
class WechatController extends BaseController
{
    /**
     * 公众号列表
     */
    public function index($key="")
    {
        $model = Db::name('wechat');
        $where=array();
        if(!empty($key)){
            $where[] = array('title|appid','like',"%$key%");
        }
        $lists=$model->where($where)->order('ID DESC')->paginate(15);
        $this->assign('lists',$lists);
        $this->assign('page',$lists->render());
        return $this->fetch();
    }

    public function add(){
        if ($this->request->isPost()) {
            //如果用户提交数据
            $data = $this->request->post();
            $validate=new WechatValidate();
            $validate->setId(0);

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {

                if (Db::name('wechat')->insert($data)) {
                    $this->success("添加成功", url('wechat/index'));
                } else {
                    $this->error("添加失败");
                }
            }
        }
        $model=array('sort'=>99);
        $this->assign('model',$model);
        $this->assign('id',0);
        return $this->fetch('edit');
    }

    /**
     * 编辑公众号
     */
    public function edit($id)
    {
        if ($this->request->isPost()) {
            //如果用户提交数据
            $data = $this->request->post();
            $validate=new WechatValidate();
            $validate->setId($id);

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                $data['id']=$id;
                if (Db::name('wechat')->update($data)) {
                    $this->success("更新成功", url('wechat/index'));
                } else {
                    $this->error("更新失败");
                }
            }
        }

        $model = Db::name('wechat')->find($id);
        if(empty($model)){
            $this->error('数据不存在');
        }
        $this->assign('model',$model);
        $this->assign('id',$id);
        return $this->fetch();
    }
    /**
     * 删除公众号
     */
    public function delete($id)
    {
        $id = intval($id);
        $model = Db::name('wechat');
        $result = $model->delete($id);
        if($result){
            $this->success("删除成功", url('wechat/index'));
        }else{
            $this->error("删除失败");
        }
    }

    public function menu()
    {
        return $this->fetch();
    }
}