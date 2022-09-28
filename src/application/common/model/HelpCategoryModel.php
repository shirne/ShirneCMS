<?php

namespace app\common\model;


use think\Db;

/**
 * Class HelpCategoryModel
 * @package app\common\model
 */
class HelpCategoryModel extends CategoryModel
{
    protected $precache='help_';

    protected $type = [];


}