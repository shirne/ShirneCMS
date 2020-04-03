<?php

namespace app\common\model;


use app\common\core\BaseModel;

/**
 * Class ProductCommentModel
 * @package app\common\model
 */
class ProductCommentModel extends BaseModel
{
    protected $name = 'product_comment';
    protected $autoWriteTimestamp = true;

    protected $insert = ['status' ,'ip','device'];

    protected function setDeviceAttr()
    {
        return request()->isMobile()?'mobile':'pc';
    }

    protected function setIpAttr()
    {
        return request()->ip();
    }
    protected function setStatusAttr($value)
    {
        return empty($value)?0:intval($value);
    }
}