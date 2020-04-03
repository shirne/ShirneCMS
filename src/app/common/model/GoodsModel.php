<?php
namespace app\common\model;


use app\common\core\ContentModel;

/**
 * Class GoodsModel
 * @package app\common\model
 */
class GoodsModel extends ContentModel
{
    protected $autoWriteTimestamp = true;
    protected $type = ['prop_data'=>'array'];
}