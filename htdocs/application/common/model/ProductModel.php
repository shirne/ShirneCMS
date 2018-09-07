<?php
namespace app\common\model;


/**
 * Class ProductModel
 * @package app\common\model
 */
class ProductModel extends BaseModel
{
    protected $autoWriteTimestamp = true;
    protected $type = ['levels'=>'array','spec_data'=>'array','prop_data'=>'array'];
}