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
    
    public static function getAreaList($areaids){
        $lists = Db::view('postageArea','*')
            ->view('postage',['calc_type'],'postage.id=postageArea.postage_id','LEFT')
            ->whereIn('postageArea.id',$areaids)->order('postageArea.sort ASC,postageArea.id ASC')->select();
        foreach ($lists as &$item){
            $item['expresses']=force_json_decode($item['expresses']);
            $item['areas']=force_json_decode($item['areas']);
        }
        return array_column($lists,NULL,'id');
    }
    
    public static function calcolate($products,$address){
        $match_areas=[];
        $products=array_index($products,'postage_id',true);
        $nopostage_ids=[];
        foreach ($products as $postage_id=>$lists){
            if($postage_id>0) {
                $postage = static::get($postage_id);
                if(!empty($address['city']) && !empty($postage['specials'])){
                    if($postage['area_type']==1 && !in_array($address['city'],$postage['specials'])){
                        $nopostage_ids[]=$postage_id;
                        continue;
                    }elseif(in_array($address['city'],$postage['specials'])){
                        $nopostage_ids[]=$postage_id;
                        continue;
                    }
                }
                $areas = $postage->getAreas();
                $nolimit=[];
                $matched=[];
                foreach ($areas as $area){
                    $mcities=$area['areas'];
                    unset($area['areas']);
                    $area['calc_type']=$postage['calc_type'];
                    if(empty($mcities)){
                        $nolimit[]=$area;
                    }elseif(!empty($address['city']) && in_array($address['city'],$mcities)){
                        $matched[]=$area;
                    }
                }
                if(!empty($matched)){
                    $match_areas[$postage_id]=$matched;
                }elseif(!empty($nolimit)){
                    $match_areas[$postage_id]=$nolimit;
                }else{
                    $nopostage_ids[]=$postage_id;
                }
            }
        }
        if(!empty($nopostage_ids)){
            return [
                'error'=>1,
                'postage_ids'=>implode(',',$nopostage_ids),
                'postages'=>$match_areas
            ];
        }
        return [
            'error'=>0,
            'postages'=>$match_areas
        ];
    }
    
    public static function getDesc($id){
        $id = intval($id);
        if($id>0){
            $postage=static::get($id);
            if(!empty($postage)){
                $areas = $postage->getAreas();
                if(!empty($areas)){
                    if($areas[0]['first']>0){
                        if($areas[0]['free_limit']>0){
                            return '满'.floatval($areas[0]['free_limit']).'包邮';
                        }
                        return $areas[0]['first_fee'].'元';
                    }
                }
            }
        }
        return '免运费';
    }
}