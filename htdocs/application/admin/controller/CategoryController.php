<?php

namespace app\admin\controller;

use app\admin\validate\CategoryValidate;
use think\Db;

/**
 * 分类管理
 */
class CategoryController extends BaseController
{
    /**
     * 分类列表
     */
    public function index($key="")
    {
        $model = Db::name('category');
        $where=array();
        if(!empty($key)){
            $where[] = array('title|name','like',"%$key%");
        } 

        $category = $model->where($where)->order('pid ASC,sort ASC,id ASC')->select();
        $this->assign('model',getSortedCategory($category));
        return $this->fetch();
    }

    public function add($pid=0){
        $pid=intval($pid);
        if ($this->request->isPost()) {
            $data=$this->request->post();
            $validate=new CategoryValidate();
            $validate->setId();

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                $iconupload=$this->upload('category','upload_icon',true);
                if(!empty($iconupload))$data['icon']=$iconupload['url'];
                $uploaded=$this->upload('category','upload_image',true);
                if(!empty($uploaded))$data['image']=$uploaded['url'];

                $result=Db::name('category')->insert($data);
                if ($result) {
                    clearCategory();
                    $this->success("添加成功", url('category/index'));
                } else {
                    $this->error("添加失败");
                }
            }
        }
        $cate = getArticleCategories();
        $model=array('sort'=>99,'pid'=>$pid);
        $this->assign('cate',$cate);
        $this->assign('model',$model);
        $this->assign('id',0);
        return $this->fetch('edit');
    }

    /**
     * 编辑分类
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
                $iconupload=$this->upload('category','upload_icon',true);
                if(!empty($iconupload)){
                    $data['icon']=$iconupload['url'];
                    $delete_images[]=$data['delete_icon'];
                }
                $uploaded=$this->upload('category','upload_image',true);
                if(!empty($uploaded)){
                    $data['image']=$uploaded['url'];
                    $delete_images[]=$data['delete_image'];
                }
                unset($data['delete_icon']);
                unset($data['delete_image']);

                $result=Db::name('category')->where(array('id'=>$id))->update($data);

                if ($result) {
                    delete_image($delete_images);
                    clearCategory();
                    $this->success("保存成功", url('category/index'));
                } else {
                    $this->error("保存失败");
                }
            }
        }else{
            $model = Db::name('category')->find($id);
            if(empty($model)){
                $this->error('分类不存在');
            }
            $cate = getSortedCategory(Db::name('category')->order('pid ASC,sort ASC')->select());

            $this->assign('cate',$cate);
            $this->assign('model',$model);
            $this->assign('id',$id);
            return $this->fetch();
        }
    }

    /**
     * 删除分类
     */
    public function delete($id)
    {
        $id = intval($id);
        $model = Db::name('Category');
        //查询属于这个分类的文章
        $posts = Db::name('Post')->where(["cate_id"=>$id])->select();
        if($posts){
            $this->error("禁止删除含有文章的分类");
        }
        //禁止删除含有子分类的分类
        $hasChild = $model->where(["pid"=>$id])->select();
        if($hasChild){
            $this->error("禁止删除含有子分类的分类");
        }
        //验证通过
        $result = $model->delete($id);
        if($result){
            clearCategory();
            $this->success("分类删除成功", url('category/index'));
        }else{
            $this->error("分类删除失败");
        }
    }
}
