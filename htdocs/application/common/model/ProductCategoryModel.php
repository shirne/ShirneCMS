<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/5/11
 * Time: 22:23
 */

namespace app\common\model;


use think\Db;

class ProductCategoryModel extends CategoryModel
{
    protected $precache='product_';

    protected $type = ['specs'=>'array'];

    protected function _get_data(){
        return Db::name('ProductCategory')->order('pid ASC,sort ASC,id ASC')->select();
    }
}