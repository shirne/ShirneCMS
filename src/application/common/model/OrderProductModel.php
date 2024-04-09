<?php

namespace app\common\model;


use app\common\core\BaseModel;

/**
 * Class OrderProductModel
 * @package app\common\model
 */
class OrderProductModel extends BaseModel
{
    protected $pk = 'id';
    protected $type = ['sku_specs' => 'array'];
}
