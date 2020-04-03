<?php

namespace app\common\model;


use think\facade\Db;

/**
 * Class HelpCategoryModel
 * @package app\common\model
 */
class HelpCategoryModel extends CategoryModel
{
    protected $precache='help_';

    protected $type = [];

    protected function _get_data(){
        return Db::name('HelpCategory')->order('pid ASC,sort ASC,id ASC')->select();
    }

}