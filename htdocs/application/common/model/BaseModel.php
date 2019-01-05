<?php

namespace app\common\model;

use think\Db;
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

    protected static $instance=null;
    public static function getInstance(){
        if(!self::$instance){
            self::$instance=new static();
        }
        return self::$instance;
    }

    protected function triggerStatus($item,$status)
    {

    }

    /**
     * @param $toStatus int|array
     * @param $where string|array|int
     */
    public function updateStatus($toStatus,$where){
        if(is_array($toStatus)){
            $data=$toStatus;
        }else{
            $data['status']=$toStatus;
        }

        $lists=Db::name($this->name)->where($where)->select();
        Db::name($this->name)->where($where)->update($data);
        foreach ($lists as $item){
            if($item['status']!=$data['status']){
                $this->triggerStatus($item,$data['status']);
            }
        }
    }
}