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
    
    public static function updateAreas($newareas, $id){
        $exists = Db::name('postageArea')->where('postage_id',$id)->select();
        $exists = array_column($exists,NULL,'id');
        $sort=0;
        foreach ($newareas as $area_id=>$area){
            $area['sort']=$sort;
            if(!empty($area['expresses']))$area['expresses']=json_encode($area['expresses'],JSON_UNESCAPED_UNICODE);
            else $area['expresses']='';
            if(!empty($area['areas']))$area['areas']=json_encode($area['areas'],JSON_UNESCAPED_UNICODE);
            else $area['areas']='';
            unset($area['id']);
            if(is_numeric($area_id) && isset($exists[$area_id])){
                Db::name('postageArea')->where('id',$area_id)->update($area);
            }else{
                $area['postage_id']=$id;
                Db::name('postageArea')->insert($area);
            }
            $sort++;
        }
    }
    
    public function getAreas(){
        $lists = Db::name('postageArea')->where('postage_id',$this['id'])->order('sort ASC,id ASC')->select();
        foreach ($lists as &$item){
            $item['expresses']=force_json_decode($item['expresses']);
            $item['areas']=force_json_decode($item['areas']);
        }
        return $lists;
    }
}