<?php

namespace addon\credit_shop\core\model;

use app\common\model\CategoryModel;
use think\Db;

class GoodsCategoryModel extends CategoryModel
{
    protected $precache='goods_';

    protected function _get_data(){
        return Db::name('GoodsCategory')->order('pid ASC,sort ASC,id ASC')->select();
    }
}