<?php

namespace addon\credit_shop\core\facade;


use app\common\core\SimpleFacade;

/**
 * Class CategoryFacade
 * @package app\common\facade
 * @see \addon\credit_shop\core\model\GoodsCategoryModel
 * @method static array getCategories($force=false) 获取分类列表
 * @method static array findCategory($idorname) 查找分类
 * @method static array getCategoryId($idorname) 查找分类ID
 * @method static array getCategoryTree($idorname) 获取当前分类所在的层级树
 * @method static array getTreedCategory($force=false) 获取排序后的分类
 * @method static array getSubCateIds($pid) 获取下级分类id列表
 * @method static void clearCache() 清除缓存
 */
class CategoryFacade extends SimpleFacade
{
    protected static function getFacadeClass(){
        return \addon\credit_shop\core\model\GoodsCategoryModel::class;
    }
}