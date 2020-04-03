<?php
namespace app\admin\controller;

use app\admin\validate\KeywordsValidate;
use think\Db;

/**
 * 关键字管理
 * Class KeywordsController
 * @package app\admin\controller
 */
class KeywordsController extends BaseController
{
    /**
     * 关键字列表
     * @param string $key
     * @return mixed|\think\response\Redirect
     */
    public function index($key="")
    {
        if($this->request->isPost()){
            return redirect(url('',['key'=>base64_encode($key)]));
        }
        $key=empty($key)?"":base64_decode($key);
        $model = Db::name('keywords');
        
        if(!empty($key)){
            $model->whereLike('title|description',"%$key%");
        }
        $lists=$model->order('ID DESC')->paginate(15);
        $this->assign('lists',$lists);
        $this->assign('page',$lists->render());
        return $this->fetch();
    }

    /**
     * 添加关键字
     * @return mixed
     */
    public function add(){
        if ($this->request->isPost()) {
            //如果用户提交数据
            $data = $this->request->post();
            $validate=new KeywordsValidate();
            $validate->setId(0);

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                
                

                if (Db::name('keywords')->insert($data)) {
                    $this->success(lang('Add success!'), url('keywords/index'));
                } else {
                    $this->error(lang('Add failed!'));
                }
            }
        }
        $model=array('sort'=>99);
        $this->assign('model',$model);
        $this->assign('groups',$this->getGroups());
        $this->assign('id',0);
        return $this->fetch('edit');
    }

    /**
     * 编辑关键字
     * @param $id
     * @return mixed
     */
    public function edit($id)
    {
        if ($this->request->isPost()) {
            //如果用户提交数据
            $data = $this->request->post();
            $validate=new KeywordsValidate();
            $validate->setId($id);

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {

                $data['id']=$id;
                if (Db::name('Links')->update($data)) {
                    $this->success(lang('Update success!'), url('keywords/index'));
                } else {
                    $this->error(lang('Update failed!'));
                }
            }
        }

        $model = Db::name('Keywords')->find($id);
        if(empty($model)){
            $this->error('关键字不存在');
        }
        $this->assign('groups',$this->getGroups());
        $this->assign('model',$model);
        $this->assign('id',$id);
        return $this->fetch();
    }

    private function getGroups(){
        $groups = Db::name('keywords')->where('group','neq','')->distinct('group')->field('group')->select();

        if(!empty($groups)){
            return array_column($groups,'group');
        }
        return ['global','product','article'];
    }

    /**
     * 删除关键字
     * @param $id
     */
    public function delete($id)
    {
        $id = intval($id);
        $model = Db::name('keywords');
        $result = $model->delete($id);
        if($result){
            $this->success(lang('Delete success!'), url('keywords/index'));
        }else{
            $this->error(lang('Delete failed!'));
        }
    }
}
