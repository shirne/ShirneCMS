<?php

namespace app\common\model;

use app\common\core\BaseModel;

class OrderRefundModel extends BaseModel
{
    protected $name = 'order_refund';
    protected $autoWriteTimestamp = true;
    protected $type = ['product'=>'array','address'=>'array','express'=>'array'];

}