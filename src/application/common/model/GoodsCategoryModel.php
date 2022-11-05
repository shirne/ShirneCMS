<?php

namespace app\common\model;


use think\Db;

class GoodsCategoryModel extends CategoryModel
{
    protected $precache='goods_';

    protected $type = ['props'=>'array'];

    protected function _get_data(){
        return Db::name('GoodsCategory')->order('pid ASC,sort ASC,id ASC')->select();
    }
}