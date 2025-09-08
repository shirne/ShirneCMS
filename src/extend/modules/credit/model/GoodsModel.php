<?php

namespace modules\credit\model;


use app\common\core\ContentModel;
use modules\credit\facade\GoodsCategoryFacade;

/**
 * Class GoodsModel
 * @package modules\credit\model
 */
class GoodsModel extends ContentModel
{
    protected $autoWriteTimestamp = true;
    protected $type = ['prop_data' => 'array'];

    function __construct($data = [])
    {
        parent::__construct($data);

        $this->cateFacade = GoodsCategoryFacade::getFacadeInstance();
        $this->searchFields = 'title|goods_no|description';
    }
}
