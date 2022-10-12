<?php
namespace app\admin\controller;

use app\admin\validate\LinksValidate;
use app\common\model\LinksModel;
use think\Db;

/**
 * 链接管理
 * Class LinksController
 * @package app\admin\controller
 */
class LinksController extends BaseController
{
    /**
     * 链接列表
     * @param string $key
     * @return mixed|\think\response\Redirect
     */
    public function index($key="")
    {
        if($this->request->isPost()){
            return redirect(url('',['key'=>base64url_encode($key)]));
        }
        $key=empty($key)?"":base64url_decode($key);
        $model = Db::name('links');
        $where=array();
        if(!empty($key)){
            $where[] = array('title|url','like',"%$key%");
        }
        $lists=$model->where($where)->order('ID DESC')->paginate(15);
        $this->assign('lists',$lists);
        $this->assign('page',$lists->render());
        $this->assign('groups',$this->getGroups());
        return $this->fetch();
    }

    /**
     * 添加链接
     * @return mixed
     */
    public function add(){
        if ($this->request->isPost()) {
            //如果用户提交数据
            $data = $this->request->post();
            $validate=new LinksValidate();

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                $uploaded=$this->_upload('links','upload_logo');
                if(!empty($uploaded)){
                    $data['logo']=$uploaded['url'];
                }elseif($this->uploadErrorCode>102){
                    $this->error($this->uploadErrorCode.':'.$this->uploadError);
                }

                if (Db::name('Links')->insert($data)) {
                    $this->success(lang('Add success!'), url('links/index'));
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
     * 编辑链接
     * @param $id
     * @return mixed
     */
    public function edit($id)
    {
        if ($this->request->isPost()) {
            //如果用户提交数据
            $data = $this->request->post();
            $validate=new LinksValidate();

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                $delete_images=[];
                $uploaded=$this->_upload('links','upload_logo');
                if(!empty($uploaded)){
                    $data['logo']=$uploaded['url'];
                    $delete_images[]=$data['delete_logo'];
                }elseif($this->uploadErrorCode>102){
                    $this->error($this->uploadErrorCode.':'.$this->uploadError);
                }
                unset($data['delete_logo']);

                $data['id']=$id;
                if (Db::name('Links')->update($data)) {
                    delete_image($delete_images);
                    $this->success(lang('Update success!'), url('links/index'));
                } else {
                    $this->error(lang('Update failed!'));
                }
            }
        }

        $model = Db::name('Links')->find($id);
        if(empty($model)){
            $this->error('链接不存在');
        }
        $this->assign('model',$model);
        $this->assign('groups',$this->getGroups());
        $this->assign('id',$id);
        return $this->fetch();
    }

    private function getGroups(){
        return LinksModel::getGroups();
    }

    /**
     * 删除链接
     * @param $id
     */
    public function delete($id)
    {
        $id = intval($id);
        $model = Db::name('links');
        $result = $model->delete($id);
        if($result){
            $this->success(lang('Delete success!'), url('links/index'));
        }else{
            $this->error(lang('Delete failed!'));
        }
    }
}
