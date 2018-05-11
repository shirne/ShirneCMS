<?php
/**
 * 产品分类
 * User: shirne
 * Date: 2018/5/11
 * Time: 22:24
 */

namespace app\common\facade;


use think\Facade;

/**
 * Class ProductCategoryModel
 * @package app\common\facade
 * @see \app\common\model\CategoryModel
 * @method array getCategories($force=false) static 获取分类列表
 * @method array findCategory($idorname) static 查找分类
 * @method array getCategoryId($idorname) static 查找分类ID
 * @method array getCategoryTree($idorname) static 获取当前分类所在的层级树
 * @method array getTreedCategory($force=false) static 获取排序后的分类
 * @method array getSubCateIds($pid) static 获取下级分类id列表
 * @method void clearCache() static 清除缓存
 */
class ProductCategoryModel extends Facade
{
    protected static function getFacadeClass(){
        return \app\common\model\ProductCategoryModel::class;
    }
}