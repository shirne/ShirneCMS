<?php

namespace addon\credit_shop\core\facade;


use app\common\core\SimpleFacade;

/**
 * Class CategoryFacade
 * @package app\common\facade
 * @see \addon\credit_shop\core\model\GoodsCategoryModel
 * @method array getCategories($force=false) static 获取分类列表
 * @method array findCategory($idorname) static 查找分类
 * @method array getCategoryId($idorname) static 查找分类ID
 * @method array getCategoryTree($idorname) static 获取当前分类所在的层级树
 * @method array getTreedCategory($force=false) static 获取排序后的分类
 * @method array getSubCateIds($pid) static 获取下级分类id列表
 * @method void clearCache() static 清除缓存
 */
class CategoryFacade extends SimpleFacade
{
    protected static function getFacadeClass(){
        return \addon\credit_shop\core\model\GoodsCategoryModel::class;
    }
}