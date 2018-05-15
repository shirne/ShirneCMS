<?php
namespace app\common\model;

use think\Model;

class ProductModel extends Model
{
    protected $autoWriteTimestamp = true;
    protected $type = ['levels'=>'array','spec_data'=>'array'];
}