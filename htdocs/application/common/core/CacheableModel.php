<?php


namespace app\common\core;

/**
 * 具有缓存数据方法的模型类
 * Class CacheableModel
 * @package app\common\core
 */
class CacheableModel extends BaseModel
{
    protected $cacheKey='';
    private $cacheData=null;
    
    public static function getCacheData($force=false){
        return static::getInstance()->_get_cache_data($force);
    }
    public static function clearCacheData(){
        static::getInstance()->_clear_cache_data();
    }
    
    private function _get_cache_data($force=false){
        if(empty($this->cacheKey)){
            $this->cacheKey = strtolower(str_replace(['/','\\'],'_',static::class)).'_cache_key';
        }
        if($force || empty($this->cacheData)){
            if(!$force)$this->cacheData=cache($this->cacheKey);
            if(empty($this->cacheData)){
                $this->cacheData = $this->get_cache_data();
                cache($this->cacheKey,$this->cacheData);
            }
        }
        return $this->cacheData;
    }
    private function _clear_cache_data(){
        if($this->cacheKey){
            $this->cacheData=null;
            cache($this->cacheKey,null);
        }
    }
    
    protected function get_cache_data()
    {
        return [];
    }
}