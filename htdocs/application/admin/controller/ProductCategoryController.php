<?php
/**
 * 商品分类
 * User: shirne
 * Date: 2018/5/11
 * Time: 17:48
 */

namespace app\admin\controller;


use app\admin\model\SpecificationsModel;
use app\admin\validate\ProductCategoryValidate;
use app\common\facade\ProductCategoryFacade;
use app\common\model\ProductCategoryModel;
use think\Db;

class ProductCategoryController extends BaseController
{
    public function index(){
        $this->assign('model',ProductCategoryFacade::getCategories(true));
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
                $iconupload=$this->upload('category','upload_icon');
                if(!empty($iconupload))$data['icon']=$iconupload['url'];
                $uploaded=$this->upload('category','upload_image');
                if(!empty($uploaded))$data['image']=$uploaded['url'];

                $model=ProductCategoryModel::create($data);
                if ($model['id']) {
                    ProductCategoryFacade::clearCache();
                    $this->success("添加成功", url('productCategory/index'));
                } else {
                    $this->error("添加失败");
                }
            }
        }
        $cate = ProductCategoryFacade::getCategories();
        $model=array('sort'=>99,'pid'=>$pid,'specs'=>[]);
        $this->assign('cate',$cate);
        $this->assign('model',$model);
        $this->assign('specs',SpecificationsModel::getList());
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

                ProductCategoryModel::update($data,['id'=>$id]);

                delete_image($delete_images);
                ProductCategoryFacade::clearCache();
                $this->success("保存成功", url('productCategory/index'));
            }
        }else{
            $model = ProductCategoryModel::get($id);
            if(empty($model) || empty($model['id'])){
                $this->error('分类不存在');
            }
            $cate = ProductCategoryFacade::getCategories();
            if(is_null($model->specs)){
                $model->specs=[];
            }

            $this->assign('cate',$cate);
            $this->assign('model',$model);
            $this->assign('specs',SpecificationsModel::getList());
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
            $this->error("禁止删除含有产品的分类");
        }
        //禁止删除含有子分类的分类
        $hasChild = Db::name('ProductCategory')->where('pid','in',$id)->count();
        if($hasChild){
            $this->error("禁止删除含有子分类的分类");
        }
        //验证通过
        $result = Db::name('ProductCategory')->where('id','in',$id)->delete();
        if($result){
            ProductCategoryFacade::clearCache();
            $this->success("分类删除成功", url('productCategory/index'));
        }else{
            $this->error("分类删除失败");
        }
    }
}