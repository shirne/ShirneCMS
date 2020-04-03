<?php

namespace app\common\model;


use think\facade\Db;

class GoodsCategoryModel extends CategoryModel
{
    protected $name = 'goods_category';
    protected $precache='goods_';

    protected function _get_data(){
        return Db::name('GoodsCategory')->order('pid ASC,sort ASC,id ASC')->select();
    }
}