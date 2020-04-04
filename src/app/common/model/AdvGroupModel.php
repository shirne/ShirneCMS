<?php

namespace app\common\model;


use app\common\core\BaseModel;
use think\facade\Db;

/**
 * Class AdvGroupModel
 * @package app\common\model
 */
class AdvGroupModel extends BaseModel
{
    protected $name = 'adv_group';
    protected $type = ['ext_set'=>'array'];
    
    public static function getAdList($flag,$limit=10)
    {
        $model=self::where(['flag'=>$flag])->find();
        if(empty($model)){
            return [];
        }
        $time=strtotime(date('Y-m-d'));
        $lists =  Db::name('AdvItem')
            ->where('group_id',$model->id)
            ->where('status',1)
            ->where(function($query) use ($time){
                $query->where('start_date', 0)
                    ->whereOr('start_date', '<=', $time);
            })
            ->where(function($query) use ($time){
                $query->where('end_date', 0)
                    ->whereOr('end_date', '>=', $time);
            })
            ->order('sort ASC, id DESC')
            ->limit($limit)
            ->select();
        return self::fixAdItem($lists, true);
    }

    public static function getAdItem($flag)
    {
        $model=self::where(['flag'=>$flag])->find();
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