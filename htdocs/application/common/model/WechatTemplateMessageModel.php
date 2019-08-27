<?php


namespace app\common\model;


class WechatTemplateMessageModel extends BaseModel
{
    protected $autoWriteTimestamp = true;
    
    protected static $tpls=[];
    public static function getTpls($wxid,$type=false){
        if(!isset(self::$tpls[$wxid])){
            $tpls=static::where('wechat_id',$wxid)->select();
            self::$tpls[$wxid] = array_column($tpls->toArray(),null,'type');
        }
        if($type){
            return self::$tpls[$wxid][$type]?:null;
        }
        return self::$tpls[$wxid];
    }
}