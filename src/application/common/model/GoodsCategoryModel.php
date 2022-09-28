<?php

namespace app\common\model;


use think\Db;

class GoodsCategoryModel extends CategoryModel
{
    protected $precache='goods_';

    protected $type = ['props'=>'array'];

}