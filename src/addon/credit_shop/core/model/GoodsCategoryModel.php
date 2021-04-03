<?php

namespace addon\credit_shop\core\model;

use app\common\model\CategoryModel;
use think\facade\Db;

class GoodsCategoryModel extends CategoryModel
{
    protected $table = 'goods_category';
    protected $precache='goods_';

    protected function _get_data(){
        return Db::name('GoodsCategory')->order('pid ASC,sort ASC,id ASC')->select();
    }
}