<?php

namespace app\common\model;


use think\facade\Db;

/**
 * Class ProductCategoryModel
 * @package app\common\model
 */
class ProductCategoryModel extends CategoryModel
{
    protected $name = 'product_category';
    protected $precache='product_';

    protected $type = ['specs'=>'array','props'=>'array'];

    protected function _get_data(){
        return Db::name('ProductCategory')->order('pid ASC,sort ASC,id ASC')->select();
    }

    public function getBrandsCategories($brandid){
        $catebrand = Db::name('productCategoryBrand')->where('brand_id',$brandid)->select();
        return array_column($catebrand,'cate_id');
    }

    public function getBrands($cateid = 0, $key = ''){
        $model = Db::view('productBrand','*')
            ->view('productCategoryBrand','cate_id','productCategoryBrand.brand_id=productBrand.id','LEFT');
        if($cateid!=0){
            $topCate = $this->getTopCategory($cateid);
            if(!empty($topCate)){
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