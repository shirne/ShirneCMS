<?php

namespace app\admin\controller\shop;

use app\admin\controller\BaseController;
use app\admin\validate\ProductBrandValidate;
use app\common\facade\ProductCategoryFacade;
use think\facade\Db;

/**
 * 商品品牌管理
 * Class BrandController
 * @package app\admin\controller
 */
class BrandController extends BaseController
{
    public function search($cateid=0, $key = '')
    {
        $lists = ProductCategoryFacade::getBrands($cateid, $key);
        return json(['data'=>$lists,'code'=>1]);
    }

    /**
     * 品牌列表
     * @param string $key
     * @return mixed|\think\response\Redirect
     */
    public function index($key="")
    {
        if($this->request->isPost()){
            return redirect(url('',['key'=>base64url_encode($key)]));
        }
        $key=empty($key)?"":base64url_decode($key);
        $model = Db::name('productBrand');
        if(!empty($key)){
            $model->whereLike('title|url',"%$key%")
        }
        $lists=$model->order('ID DESC')->paginate(15);
        $this->assign('lists',$lists);
        $this->assign('page',$lists->render());
        return $this->fetch();
    }

    /**
     * 添加品牌
     * @return mixed
     */
    public function add(){
        if ($this->request->isPost()) {
            //如果用户提交数据
            $data = $this->request->post();
            $validate=new ProductBrandValidate();

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                $uploaded=$this->upload('brand','upload_logo');
                if(!empty($uploaded)){
                    $data['logo']=$uploaded['url'];
                }elseif($this->uploadErrorCode>102){
                    $this->error($this->uploadErrorCode.':'.$this->uploadError);
                }

                $cates = $data['cates'];
                unset($data['cates']);
                $insertid=Db::name('productBrand')->insert($data,false,true);
                if ($insertid) {
                    if(!empty($cates)){
                        foreach($cates as $cid){
                            Db::name('productCategoryBrand')->insert([
                                'cate_id'=>$cid,
                                'brand_id'=>$insertid
                            ]);
                        }
                    }
                    $this->success(lang('Add success!'), url('shop.brand/index'));
                } else {
                    $this->error(lang('Add failed!'));
                }
            }
        }
        $model=array('sort'=>99);
        $this->assign('model',$model);
        $this->assign('cates',ProductCategoryFacade::getSubCategory(0));
        $this->assign('id',0);
        return $this->fetch('edit');
    }

    /**
     * 编辑品牌
     * @param $id
     * @return mixed
     */
    public function edit($id)
    {
        if ($this->request->isPost()) {
            //如果用户提交数据
            $data = $this->request->post();
            $validate=new ProductBrandValidate();

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                $delete_images=[];
                $uploaded=$this->upload('brand','upload_logo');
                if(!empty($uploaded)){
                    $data['logo']=$uploaded['url'];
                    $delete_images[]=$data['delete_logo'];
                }elseif($this->uploadErrorCode>102){
                    $this->error($this->uploadErrorCode.':'.$this->uploadError);
                }
                unset($data['delete_logo']);

                $cates = empty($data['cates'])?[]:$data['cates'];
                unset($data['cates']);
                $data['id']=$id;
                try{
                    Db::name('productBrand')->update($data);
                    delete_image($delete_images);
                    $catecheckes = ProductCategoryFacade::getBrandsCategories($id);
                    $deletes = array_diff($catecheckes,$cates);
                    if(!empty($deletes)){
                        Db::name('productCategoryBrand')->whereIn('cate_id',$deletes)->delete();
                    }
                    $newids=array_diff($cates,$catecheckes);
                    if(!empty($newids)){
                        foreach($newids as $cid){
                            Db::name('productCategoryBrand')->insert([
                                'cate_id'=>$cid,
                                'brand_id'=>$id
                            ]);
                        }
                    }
                }catch(\Exception $err){
                    $this->error(lang('Update failed: %',[$err->getMessage()]));
                }
                
                $this->success(lang('Update success!'), url('shop.brand/index'));
            }
        }

        $model = Db::name('productBrand')->find($id);
        if(empty($model)){
            $this->error('品牌不存在');
        }
        $catecheckes = ProductCategoryFacade::getBrandsCategories($id);
        $cates=ProductCategoryFacade::getSubCategory(0);
        foreach($cates as &$cate){
            if(in_array($cate['id'],$catecheckes)){
                $cate['checked']=1;
            }else{
                $cate['checked']=0;
            }
        }

        $this->assign('model',$model);
        $this->assign('cates',$cates);
        $this->assign('id',$id);
        return $this->fetch();
    }

    /**
     * 删除品牌
     * @param $id
     */
    public function delete($id)
    {
        $id = intval($id);
        $model = Db::name('productBrand');
        $result = $model->delete($id);
        if($result){
            $this->success(lang('Delete success!'), url('shop.brand/index'));
        }else{
            $this->error(lang('Delete failed!'));
        }
    }
}