<?php

namespace app\common\model;


/**
 * Class ProductSkuModel
 * @package app\common\model
 */
class ProductSkuModel extends BaseModel
{
    protected $pk='sku_id';
    protected $type = ['specs'=>'array','ext_price'=>'array'];
}