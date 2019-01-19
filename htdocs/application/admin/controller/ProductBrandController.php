<?php

namespace app\admin\controller;
use app\admin\validate\ProductBrandValidate;
use app\common\facade\ProductCategoryFacade;
use think\Db;

/**
 * 商品品牌管理
 * Class ProductBrandController
 * @package app\admin\controller
 */
class ProductBrandController extends BaseController
{
    public function search($cateid)
    {
        $lists = ProductCategoryFacade::getBrands($cateid);
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
            return redirect(url('',['key'=>base64_encode($key)]));
        }
        $key=empty($key)?"":base64_decode($key);
        $model = Db::name('productBrand');
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

                if (Db::name('productBrand')->insert($data)) {
                    $this->success(lang('Add success!'), url('productBrand/index'));
                } else {
                    $this->error(lang('Add failed!'));
                }
            }
        }
        $model=array('sort'=>99);
        $this->assign('model',$model);
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
                unset($data['delete_image']);

                $data['id']=$id;
                if (Db::name('productBrand')->update($data)) {
                    delete_image($delete_images);
                    $this->success(lang('Update success!'), url('productBrand/index'));
                } else {
                    $this->error(lang('Update failed!'));
                }
            }
        }

        $model = Db::name('productBrand')->find($id);
        if(empty($model)){
            $this->error('品牌不存在');
        }
        $this->assign('model',$model);
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
            $this->success(lang('Delete success!'), url('productBrand/index'));
        }else{
            $this->error(lang('Delete failed!'));
        }
    }
}