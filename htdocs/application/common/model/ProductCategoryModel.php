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

    protected function _get_data(){
        return Db::name('ProductCategory')->order('pid ASC,sort ASC,id ASC')->select();
    }

    public function getBrands($cateid){
        $model = Db::view('productBrand','*')
            ->view('productCategoryBrand','cate_id','productCategoryBrand.brand_id=productBrand.id');
        if($cateid!=0){
            $topCate = $this->getTopCategory($cateid);
            if(!empty($topCate)){
                $model->where('cate_id',$topCate['id']);
            }else {
                return [];
            }
        }

        return $model->order('productBrand.sort ASC,productBrand.id DESC')
            ->select();
    }
}