<?php

namespace app\admin\controller;

use app\admin\validate\OauthValidate;
use think\Db;


/**
 * 第三方登录配置
 * Class OauthController
 * @package app\admin\controller
 */
class OauthController extends BaseController
{
    /**
     * 列表
     */
    public function index($key="")
    {
        $model = Db::name('OAuth');
        $lists=$model->order('ID ASC')->paginate(15);
        $this->assign('lists',$lists);
        $this->assign('page',$lists->render());
        $this->assign('types',getOauthTypes());
        return $this->fetch();
    }

    public function add(){
        if ($this->request->isPost()) {
            //如果用户提交数据
            $data = $this->request->post();
            $validate=new OauthValidate();

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {

                if (Db::name('OAuth')->insert($data)) {
                    $this->success("添加成功", url('oauth/index'));
                } else {
                    $this->error("添加失败");
                }
            }
        }
        $model=array();
        $this->assign('types',getOauthTypes());
        $this->assign('model',$model);
        $this->assign('id',0);
        return $this->fetch('edit');
    }

    /**
     * 编辑
     */
    public function edit($id)
    {
        if ($this->request->isPost()) {
            //如果用户提交数据
            $data = $this->request->post();
            $validate=new OauthValidate();

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                $data['id']=$id;
                if (Db::name('OAuth')->update($data)) {
                    $this->success("更新成功", url('oauth/index'));
                } else {
                    $this->error("更新失败");
                }
            }
        }

        $model = Db::name('OAuth')->find($id);
        if(empty($model)){
            $this->error('数据不存在');
        }
        $this->assign('types',getOauthTypes());
        $this->assign('model',$model);
        $this->assign('id',$id);
        return $this->fetch();
    }
    /**
     * 删除
     */
    public function delete($id)
    {
        $id = intval($id);
        $model = Db::name('OAuth');
        $result = $model->delete($id);
        if($result){
            $this->success("链接删除成功", url('oauth/index'));
        }else{
            $this->error("链接删除失败");
        }
    }
}