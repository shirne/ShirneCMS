<?php

namespace app\common\facade;


use app\common\core\SimpleFacade;

/**
 * Class ProductCategoryFacade
 * @package app\common\facade
 * @see \app\common\model\ProductCategoryModel
 * @method static array getAllCategories($force=false) 获取全部分类列表，无缓存
 * @method static array getCategories($force=false) 获取分类列表
 * @method static array findCategory($idorname) 查找分类
 * @method static array findCategories($idorname) 查找分类
 * @method static array getCategoryId($idorname) 查找分类ID
 * @method static array getCategoryTree($idorname) 获取当前分类所在的层级树
 * @method static array getTopCategory($idorname) 获取顶级分类
 * @method static array getBrandsCategories($brandid) 获取指定品牌关联的类目
 * @method static array getBrands($cateid = 0, $key = '') 获取分类绑定的品牌(按顶级类获取)
 * @method static array getTreedCategory($force=false) 获取排序后的分类
 * @method static array getSubCategory($pid=0) 获取指定id的下级分类列表
 * @method static array getSubCateIds($pid, $recursive=false) 获取下级分类id列表
 * @method static void clearCache() 清除缓存
 */
class ProductCategoryFacade extends SimpleFacade
{
    protected static function getFacadeClass(){
        return \app\common\model\ProductCategoryModel::class;
    }

}