<?php

namespace app\common\facade;



use app\common\core\SimpleFacade;

/**
 * Class CategoryFacade
 * @package app\common\facade
 * @see \app\common\model\CategoryModel
 * @method static array getCategories($force = false) 获取分类列表
 * @method static array findCategoryByAttr($attr, $value) 查找分类
 * @method static array findCategory($idorname) 查找分类
 * @method static array findCategories($idornames) 查找分类
 * @method static array getCategoryId($idorname) 查找分类ID
 * @method static array getCategoryIds($idornames) 查找分类ID
 * @method static array getCategoryTree($idorname) 获取当前分类所在的层级树
 * @method static array getTopCategory($idorname) 获取顶级分类
 * @method static array getTreedCategory($force = false) 获取排序后的分类
 * @method static array getSubCategory($pid = 0, $maxlevel = 1) 获取指定id的下级分类列表
 * @method static array getSubCateNames($pid, $recursive=false, $includeSelf = true) (递归)获取下级分类名称列表
 * @method static array getSubCateIds($pid, $recursive=false, $includeSelf = true) (递归)获取下级分类id列表
 * @method static void clearCache() 清除缓存
 */
class CategoryFacade extends SimpleFacade
{
    protected static function getFacadeClass()
    {
        return \app\common\model\CategoryModel::class;
    }
}
