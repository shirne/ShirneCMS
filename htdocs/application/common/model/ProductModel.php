<?php
namespace app\common\model;
use think\Db;


/**
 * Class ProductModel
 * @package app\common\model
 */
class ProductModel extends BaseModel
{
    protected $autoWriteTimestamp = true;
    protected $type = ['levels'=>'array','spec_data'=>'array','prop_data'=>'array'];

    private static $specifications;
    public static function getSpecList(){
        if (empty(self::$specifications)) {
            self::$specifications = cache('specifications');
            if (empty(self::$specifications)) {
                $data =  Db::name('specifications')->field(['id','title'])->order('id ASC')->select();
                self::$specifications=array_index($data,'id,title');
                cache('specifications', self::$specifications);
            }
        }
        return self::$specifications;
    }

    public static function transSpec($sku_specs){
        $transed=[];
        $specs=self::getSpecList();
        $unindex=0;
        foreach ($sku_specs as $id=>$value){
            if(isset($specs[$id])){
                $transed[$specs[$id]]=$value;
            }else{
                $transed['unknown-'.$unindex++]=$value;
            }
        }
        return $transed;
    }
}