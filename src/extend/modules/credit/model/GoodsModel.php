<?php

namespace modules\credit\model;


use app\common\core\ContentModel;

/**
 * Class GoodsModel
 * @package modules\credit\model
 */
class GoodsModel extends ContentModel
{
    protected $autoWriteTimestamp = true;
    protected $type = ['prop_data' => 'array'];
}