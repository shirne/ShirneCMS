<?php

namespace app\admin\controller\shop;

use app\admin\controller\BaseController;
use app\admin\model\SpecificationsModel;
use app\admin\validate\ProductCategoryValidate;
use app\common\facade\ProductCategoryFacade;
use app\common\model\ProductCategoryModel;
use think\facade\Db;

/**
 * 商品分类
 * Class CategoryController
 * @package app\admin\controller\shop
 */
class CategoryController extends BaseController
{
    /**
     * 分类列表
     * @return mixed
     */
    public function index(){
        $this->assign('model',ProductCategoryFacade::getCategories(true));
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
            $validate=new ProductCategoryValidate();
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

                $model=ProductCategoryModel::create($data);
                if ($model['id']) {
                    ProductCategoryFacade::clearCache();
                    $this->success(lang('Add success!'), url('shop.category/index'));
                } else {
                    $this->error(lang('Add failed!'));
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
     * 修改
     * @param $id
     * @return mixed
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
                }elseif($this->uploadErrorCode>102){
                    $this->error($this->uploadErrorCode.':'.$this->uploadError);
                }
                $uploaded=$this->upload('category','upload_image');
                if(!empty($uploaded)){
                    $data['image']=$uploaded['url'];
                    $delete_images[]=$data['delete_image'];
                }elseif($this->uploadErrorCode>102){
                    delete_image($data['icon']);
                    $this->error($this->uploadErrorCode.':'.$this->uploadError);
                }
                unset($data['delete_icon']);
                unset($data['delete_image']);
                if(empty($data['specs']))$data['specs']=[];

                try{
                    ProductCategoryModel::update($data,['id'=>$id]);

                    delete_image($delete_images);
                    ProductCategoryFacade::clearCache();
                }catch(\Exception $err){
                    $this->error(lang('Update failed: %',[$err->getMessage()]));
                }
                $this->success(lang('Update success!'), url('shop.category/index'));
            }
        }

        $model = ProductCategoryModel::find($id);
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

    /**
     * 删除
     * @param $id
     */
    public function delete($id)
    {
        $id = idArr($id);
        //查询属于这个分类的文章
        $posts = Db::name('Product')->whereIn('cate_id',$id)->count();
        if($posts){
            $this->error("禁止删除含有产品的分类");
        }
        //禁止删除含有子分类的分类
        $hasChild = Db::name('ProductCategory')->whereIn('pid',$id)->count();
        if($hasChild){
            $this->error("禁止删除含有子分类的分类");
        }
        //验证通过
        $result = Db::name('ProductCategory')->whereIn('id',$id)->delete();
        if($result){
            ProductCategoryFacade::clearCache();
            $this->success(lang('Delete success!'), url('shop.category/index'));
        }else{
            $this->error(lang('Delete failed!'));
        }
    }
}