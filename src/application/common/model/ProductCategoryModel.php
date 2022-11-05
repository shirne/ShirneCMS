<?php

namespace app\common\model;


use think\Db;

/**
 * Class ProductCategoryModel
 * @package app\common\model
 */
class ProductCategoryModel extends CategoryModel
{
    protected $precache='product_';

    protected $type = ['specs'=>'array','props'=>'array'];

    protected function _get_data($isall=false){
        $model= Db::name('ProductCategory')->order('pid ASC,sort ASC,id ASC');
        if(!$isall){
            $model->where('status',1);
        }
        return $model->select();
    }

    public function getAllCategories(){
        $tmpdata = $this->_get_data(true);
        return getSortedCategory($tmpdata);
    }

    public function getBrandsCategories($brandid){
        $catebrand = Db::name('productCategoryBrand')->where('brand_id',$brandid)->select();
        return array_column($catebrand,'cate_id');
    }

    public function getBrands($cateid = 0, $key = ''){
        $model = Db::view('productBrand','*');
        if($cateid!=0){
            $topCate = $this->getTopCategory($cateid);
            if(!empty($topCate)){
                $model->view('productCategoryBrand','cate_id','productCategoryBrand.brand_id=productBrand.id','LEFT');
                $model->where('cate_id',$topCate['id']);
            }else {
                return [];
            }
        }
        if(!empty($key)){
            $model->whereLike('productBrand.title',"%$key%");
        }

        return $model->order('productBrand.sort ASC,productBrand.id DESC')
            ->select();
    }
}