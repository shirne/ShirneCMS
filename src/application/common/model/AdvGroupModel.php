<?php

namespace app\common\model;


use app\common\core\BaseModel;
use think\Db;

/**
 * Class AdvGroupModel
 * @package app\common\model
 */
class AdvGroupModel extends BaseModel
{
    protected $type = ['ext_set'=>'array'];
    
    public static function getAdList($flag,$limit=10)
    {
        $model=self::get(['flag'=>$flag]);
        if(empty($model)){
            return [];
        }
        $time=strtotime(date('Y-m-d'));
        $lists =  Db::name('AdvItem')
            ->where('group_id',$model->id)
            ->where('status',1)
            ->where('start_date',['=',0],['<=',$time],'OR')
            ->where('end_date',['=',0],['>=',$time],'OR')
            ->order('sort ASC, id DESC')
            ->limit($limit)
            ->select();
        return self::fixAdItem($lists, true);
    }

    public static function getAdItem($flag)
    {
        $model=self::get(['flag'=>$flag]);
        if(empty($model)){
            return [];
        }
        $time=strtotime(date('Y-m-d'));
        $item = Db::name('AdvItem')
            ->where('group_id',$model->id)
            ->where('status',1)
            ->where('start_date',['=',0],['<=',$time],'OR')
            ->where('end_date',['=',0],['>=',$time],'OR')
            ->order('sort ASC, id DESC')
            ->find();
        return self::fixAdItem($item);
    }
    
    public static function fixAdItem($item, $islist=false){
        if($islist){
            foreach ($item as $k=>$itm){
                $item[$k] = self::fixAdItem($itm);
            }
            return $item;
        }
        $item['ext'] = force_json_decode($item['ext_data']);
        return $item;
    }
}