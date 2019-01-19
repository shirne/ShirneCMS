<?php

namespace app\common\facade;


use extcore\SimpleFacade;

/**
 * Class ProductCategoryFacade
 * @package app\common\facade
 * @see \app\common\model\ProductCategoryModel
 * @method array getCategories($force=false) static 获取分类列表
 * @method array findCategory($idorname) static 查找分类
 * @method array getCategoryId($idorname) static 查找分类ID
 * @method array getCategoryTree($idorname) static 获取当前分类所在的层级树
 * @method array getTopCategory($idorname) static 获取顶级分类
 * @method array getBrands($cateid) static 获取分类绑定的品牌(按顶级类获取)
 * @method array getTreedCategory($force=false) static 获取排序后的分类
 * @method array getSubCategory($pid=0) static 获取指定id的下级分类列表
 * @method array getSubCateIds($pid, $recursive=false) static 获取下级分类id列表
 * @method void clearCache() static 清除缓存
 */
class ProductCategoryFacade extends SimpleFacade
{
    protected static function getFacadeClass(){
        return \app\common\model\ProductCategoryModel::class;
    }

}