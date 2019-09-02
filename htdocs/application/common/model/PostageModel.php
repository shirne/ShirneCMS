<?php


namespace app\common\model;


use app\common\core\CacheableModel;
use think\Db;

class PostageModel extends CacheableModel
{
    protected $type = ['specials'=>'array'];
    
    public static function init()
    {
        parent::init();
        self::event('after_write',function($postage){
            if($postage->is_default) {
                Db::name('postage')->where('id','NEQ', $postage->id)
                    ->update(array('is_default' => 0));
            }
            self::clearCacheData();
        });
    }
    
    protected function get_cache_data()
    {
        return static::order('id ASC')->select()->toArray();
    }
}