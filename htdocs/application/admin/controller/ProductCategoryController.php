<?php
/**
 * 商品分类
 * User: shirne
 * Date: 2018/5/11
 * Time: 17:48
 */

namespace app\admin\controller;


use app\admin\validate\ProductCategoryValidate;
use app\common\facade\ProductCategoryModel;
use think\Db;

class ProductCategoryController extends BaseController
{
    public function index(){
        $this->assign('model',ProductCategoryModel::getCategories(true));
        return $this->fetch();
    }
    public function add($pid=0){
        $pid=intval($pid);
        if ($this->request->isPost()) {
            $data=$this->request->post();
            $validate=new ProductCategoryValidate();
            $validate->setId();

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                $iconupload=$this->upload('category','upload_icon',true);
                if(!empty($iconupload))$data['icon']=$iconupload['url'];
                $uploaded=$this->upload('category','upload_image',true);
                if(!empty($uploaded))$data['image']=$uploaded['url'];

                $result=Db::name('ProductCategory')->insert($data);
                if ($result) {
                    ProductCategoryModel::clearCache();
                    $this->success("添加成功", url('productCategory/index'));
                } else {
                    $this->error("添加失败");
                }
            }
        }
        $cate = ProductCategoryModel::getCategories();
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
            $validate=new ProductCategoryValidate();
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

                $result=Db::name('ProductCategory')->where(array('id'=>$id))->update($data);

                if ($result) {
                    delete_image($delete_images);
                    ProductCategoryModel::clearCache();
                    $this->success("保存成功", url('productCategory/index'));
                } else {
                    $this->error("保存失败");
                }
            }
        }else{
            $model = Db::name('ProductCategory')->find($id);
            if(empty($model)){
                $this->error('分类不存在');
            }
            $cate = ProductCategoryModel::getCategories();

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
        $id = idArr($id);
        //查询属于这个分类的文章
        $posts = Db::name('Product')->where('cate_id','in',$id)->count();
        if($posts){
            $this->error("禁止删除含有文章的分类");
        }
        //禁止删除含有子分类的分类
        $hasChild = Db::name('ProductCategory')->where('pid','in',$id)->count();
        if($hasChild){
            $this->error("禁止删除含有子分类的分类");
        }
        //验证通过
        $result = Db::name('ProductCategory')->where('pid','in',$id)->delete();
        if($result){
            ProductCategoryModel::clearCache();
            $this->success("分类删除成功", url('productCategory/index'));
        }else{
            $this->error("分类删除失败");
        }
    }
}