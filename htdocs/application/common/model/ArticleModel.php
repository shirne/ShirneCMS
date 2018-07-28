<?php
namespace app\common\model;

use think\Model;

class ArticleModel extends Model
{
    protected $autoWriteTimestamp = true;
    protected $type = ['prop_data'=>'array'];
}