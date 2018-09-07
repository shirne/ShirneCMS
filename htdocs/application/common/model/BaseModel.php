<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/9/7
 * Time: 23:20
 */

namespace app\common\model;


use think\facade\Log;
use think\Model;

class BaseModel extends Model
{
    protected function getRelationAttribute($name, &$item)
    {
        try{
            return parent::getRelationAttribute($name, $item);
        }catch (\InvalidArgumentException $e){
            Log::record($e->getMessage());
            return null;
        }
    }
}