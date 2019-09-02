<?php

namespace app\common\model;


use app\common\core\CacheableModel;
use think\Db;

class CreditPromotionModel extends CacheableModel
{
    public static function init()
    {
        parent::init();

        self::afterWrite(function ( $model) {
            if ($model['is_default']) {
                $current = Db::name('creditPromotion')->where($model->getWhere())->find();
                if($current) {
                    Db::name('creditPromotion')->where('id', 'NEQ', $current['id'])->update(['is_default' => 0]);
                }
            }
            self::clearCacheData();
        });
    }
    
    protected function get_cache_data()
    {
        $lists = static::order('sort ASC,id ASC')->select()->toArray();
        return array_column($lists,NULL,'id');
    }
}