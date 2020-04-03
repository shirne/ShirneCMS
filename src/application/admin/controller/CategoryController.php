<?php

namespace app\admin\controller;

use app\admin\validate\CategoryValidate;
use app\common\facade\CategoryFacade;
use think\facade\Db;

/**
 * 文章分类管理
 */
class CategoryController extends BaseController
{
    /**
     * 分类列表
     */
    public function index()
    {

        $this->assign('model',CategoryFacade::getCategories(true));
        return $this->fetch();
    }

    /**
     * 添加
     * @param int $pid
     * @return mixed
     */
    public function add($pid=0){
        $pid=intval($pid);
        if ($this->request->isPost()) {
            $data=$this->request->post();
            $validate=new CategoryValidate();
            $validate->setId();

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                $iconupload=$this->upload('category','upload_icon');
                if(!empty($iconupload))$data['icon']=$iconupload['url'];
                elseif($this->uploadErrorCode>102){
                    $this->error($this->uploadErrorCode.':'.$this->uploadError);
                }
                $uploaded=$this->upload('category','upload_image');
                if(!empty($uploaded))$data['image']=$uploaded['url'];
                elseif($this->uploadErrorCode>102){
                    delete_image($data['icon']);
                    $this->error($this->uploadErrorCode.':'.$this->uploadError);
                }

                $result=Db::name('category')->insert($data);
                if ($result) {
                    CategoryFacade::clearCache();
                    $this->success(lang('Add success!'), url('category/index'));
                } else {
                    delete_image([$data['icon'],$data['image']]);
                    $this->error(lang('Add failed!'));
                }
            }
        }
        $cate = CategoryFacade::getCategories();
        $model=array('sort'=>99,'pid'=>$pid);
        $this->assign('cate',$cate);
        $this->assign('model',$model);
        $this->assign('id',0);
        return $this->fetch('edit');
    }

    /**
     * 编辑
     * @param $id
     * @return mixed
     */
    public function edit($id)
    {
        if ($this->request->isPost()) {
            $data=$this->request->post();
            $validate=new CategoryValidate();
            $validate->setId($id);

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                $delete_images=[];
                $iconupload=$this->upload('category','upload_icon');
                if(!empty($iconupload)){
                    $data['icon']=$iconupload['url'];
                    $delete_images[]=$data['delete_icon'];
                }
                $uploaded=$this->upload('category','upload_image');
                if(!empty($uploaded)){
                    $data['image']=$uploaded['url'];
                    $delete_images[]=$data['delete_image'];
                }
                unset($data['delete_icon']);
                unset($data['delete_image']);

                $result=Db::name('category')->where('id',$id)->update($data);

                if ($result) {
                    delete_image($delete_images);
                    CategoryFacade::clearCache();
                    $this->success(lang('Update success!'), url('category/index'));
                } else {
                    delete_image([$data['icon'],$data['image']]);
                    $this->error(lang('Update failed!'));
                }
            }
        }else{
            $model = Db::name('category')->find($id);
            if(empty($model)){
                $this->error('分类不存在');
            }
            $cate = CategoryFacade::getCategories();

            $this->assign('cate',$cate);
            $this->assign('model',$model);
            $this->assign('id',$id);
            return $this->fetch();
        }
    }

    /**
     * 删除分类
     * @param $id
     */
    public function delete($id)
    {
        $id = idArr($id);
        //查询属于这个分类的文章
        $posts = Db::name('Article')->where('cate_id','in',$id)->count();
        if($posts){
            $this->error("禁止删除含有文章的分类");
        }
        //禁止删除含有子分类的分类
        $hasChild = Db::name('Category')->where('pid','in',$id)->count();
        if($hasChild){
            $this->error("禁止删除含有子分类的分类");
        }
        //验证通过
        $result = Db::name('Category')->where('id','in',$id)->delete();
        if($result){
            CategoryFacade::clearCache();
            $this->success(lang('Delete success!'), url('category/index'));
        }else{
            $this->error(lang('Delete failed!'));
        }
    }
}
