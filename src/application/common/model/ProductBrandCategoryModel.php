<?php

namespace app\common\model;


use think\Db;

/**
 * Class ProductBrandCategoryModel
 * @package app\common\model
 */
class ProductBrandCategoryModel extends CategoryModel
{
    protected $precache = 'product_brand_';

    protected function _get_data()
    {
        return Db::name('ProductBrandCategory')->order('pid ASC,sort ASC,id ASC')->select();
    }

    public function getBrands($cateid = 0, $key = '')
    {
        $model = Db::view('productBrand', '*')
            ->view('productBrandCategory', ['title' => 'cate_title'], 'productBrandCategory.id=productBrand.cate_id', 'LEFT');
        if ($cateid != 0) {
            $cates = $this->getSubCateIds($cateid, true);
            if (!empty($cates)) {
                $model->whereIn('cate_id', $cates);
            } else {
                return [];
            }
        }
        if (!empty($key)) {
            $model->whereLike('productBrand.title', "%$key%");
        }

        return $model->order('productBrand.sort ASC,productBrand.id DESC')
            ->select();
    }
}
