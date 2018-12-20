<?php

namespace app\common\model;


use think\facade\Log;
use think\Model;

/**
 * 数据模型基类
 * Class BaseModel
 * @package app\common\model
 */
class BaseModel extends Model
{
    protected function getRelationAttribute($name, &$item)
    {
        try{
            return parent::getRelationAttribute($name, $item);
        }catch (\InvalidArgumentException $e){
            Log::record($e->getMessage(),\think\Log::NOTICE);
            return null;
        }
    }
}