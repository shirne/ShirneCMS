<?php

namespace app\common\model;

use think\Db;
use think\Exception;
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

    protected static $instances=[];

    /**
     * @return static
     */
    public static function getInstance()
    {
        $class = get_called_class();
        if(!isset(static::$instances[$class])){
            static::$instances[$class] = new static();
        }
        return static::$instances[$class];
    }

    protected function triggerStatus($item,$status)
    {}

    /**
     * 用于更新需要触发状态改变的表
     * @param $toStatus int|array
     * @param $where string|array|int
     * @throws Exception
     */
    public function updateStatus($toStatus,$where=null){
        if(is_array($toStatus)){
            $data=$toStatus;
        }else{
            $data['status']=$toStatus;
        }
        if(empty($where)) {
            if($this->isExists()){
                $odata=$this->getOrigin();
                Db::name($this->name)->where($this->getWhere())->update($data);
                if ($odata['status'] != $data['status']) {
                    $this->triggerStatus($odata, $data['status']);
                }
            }else{
                throw new Exception('Update status with No data exists');
            }
        }else {
            $lists = Db::name($this->name)->where($where)->select();
            Db::name($this->name)->where($where)->update($data);
            foreach ($lists as $item) {
                if ($item['status'] != $data['status']) {
                    $this->triggerStatus($item, $data['status']);
                }
            }
        }
    }
}