<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/5/11
 * Time: 22:23
 */

namespace app\common\model;


use think\Db;

class GoodsCategoryModel extends CategoryModel
{
    protected $precache='goods_';

    protected function _get_data(){
        return Db::name('GoodsCategory')->order('pid ASC,sort ASC,id ASC')->select();
    }
}