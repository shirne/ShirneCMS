<?php

namespace app\common\model;

use app\common\core\ContentModel;
use app\common\facade\ProductBrandCategoryFacade;
use think\Db;
use think\Paginator;

class ProductBrandModel extends ContentModel
{
    protected $autoWriteTimestamp = true;

    function __construct($data = [])
    {
        parent::__construct($data);
        $this->cateFacade = ProductBrandCategoryFacade::getFacadeInstance();
        $this->searchFields = 'title|url|description';
    }
}
