<?php
namespace app\common\model;

use app\common\facade\CategoryFacade;

/**
 * Class ArticleModel
 * @package app\common\model
 */
class ArticleModel extends ContentModel
{
    protected $autoWriteTimestamp = true;
    protected $type = ['prop_data'=>'array'];

    function __construct($data = [])
    {
        parent::__construct($data);
        $this->cateFacade=CategoryFacade::class;
    }
}