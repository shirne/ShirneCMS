<?php
namespace app\admin\controller;

use app\admin\validate\LinksValidate;
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
            return redirect(url('',['key'=>base64_encode($key)]));
        }
        $key=empty($key)?"":base64_decode($key);
        $model = Db::name('links');
        $where=array();
        if(!empty($key)){
            $where[] = array('title|url','like',"%$key%");
        }
        $lists=$model->where($where)->order('ID DESC')->paginate(15);
        $this->assign('lists',$lists);
        $this->assign('page',$lists->render());
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
                $uploaded=$this->upload('links','upload_logo');
                if(!empty($uploaded)){
                    $data['logo']=$uploaded['url'];
                }elseif($this->uploadErrorCode>102){
                    $this->error($this->uploadErrorCode.':'.$this->uploadError);
                }

                if (Db::name('Links')->insert($data)) {
                    $this->success("链接添加成功", url('links/index'));
                } else {
                    $this->error("链接添加失败");
                }
            }
        }
        $model=array('sort'=>99);
        $this->assign('model',$model);
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
                $uploaded=$this->upload('adv','upload_logo');
                if(!empty($uploaded)){
                    $data['logo']=$uploaded['url'];
                    $delete_images[]=$data['delete_logo'];
                }elseif($this->uploadErrorCode>102){
                    $this->error($this->uploadErrorCode.':'.$this->uploadError);
                }
                unset($data['delete_image']);

                $data['id']=$id;
                if (Db::name('Links')->update($data)) {
                    delete_image($delete_images);
                    $this->success("更新成功", url('links/index'));
                } else {
                    $this->error("更新失败");
                }
            }
        }

        $model = Db::name('Links')->find($id);
        if(empty($model)){
            $this->error('链接不存在');
        }
        $this->assign('model',$model);
        $this->assign('id',$id);
        return $this->fetch();
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
            $this->success("链接删除成功", url('links/index'));
        }else{
            $this->error("链接删除失败");
        }
    }
}
