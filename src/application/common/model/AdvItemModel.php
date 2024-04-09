<?php

namespace app\common\model;


use app\common\core\BaseModel;
use think\Db;

/**
 * Class AdvItemModel
 * @package app\common\model
 */
class AdvItemModel extends BaseModel
{
    protected $type = ['elements' => 'array', 'ext_data' => 'array'];
}
