<?php

namespace app\common\model;


use think\Db;

class CreditPromotionModel extends BaseModel
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
            self::clearCache();
        });
    }

    private static $promotions;
    private static $cache_key;
    public static function getPromotions($force=false)
    {
        if($force)self::clearCache();

        if (empty(self::$promotions)) {
            self::$promotions = cache(self::$cache_key);
            if (empty(self::$promotions)) {
                $data =  Db::name('creditPromotion')->order('sort ASC,id ASC')->select();
                self::$promotions=array_index($data,'id');
                cache(self::$cache_key, self::$promotions);
            }
        }
        return self::$promotions;
    }

    public static function clearCache(){
        self::$promotions=null;
        cache(self::$cache_key, NULL);
    }
}