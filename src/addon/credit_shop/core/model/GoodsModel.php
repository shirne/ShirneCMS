<?php
namespace addon\credit_shop\core\model;


use app\common\core\ContentModel;

/**
 * Class GoodsModel
 * @package app\common\model
 */
class GoodsModel extends ContentModel
{
    protected $table = 'goods';
    protected $autoWriteTimestamp = true;
    protected $type = ['prop_data'=>'array'];
}