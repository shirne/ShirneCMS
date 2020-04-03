<?php


namespace app\admin\model;


use app\common\core\BaseModel;
use think\facade\Db;

class ManagerRoleModel extends BaseModel
{
    protected $autoWriteTimestamp = true;
    protected $type = ['global'=>'array','detail'=>'array'];
    
    protected static $roles;
    protected static $roles_cache_key;
    public static function init()
    {
        parent::init();
    
        self::event('after_write', function ($role) {
            cache(self::$roles_cache_key,null);
        });
    }
    
    public static function getRoles($force=false){
        if(empty(self::$roles) || $force){
            self::$roles = cache(self::$roles_cache_key);
            if(empty(self::$roles) || $force){
                $roles = static::order('type ASC')->select();
                self::$roles = array_column($roles->toArray(),NULL,'type');
            }
        }
        return self::$roles;
    }
    
    public function hasGlobalPerm($item){
        return in_array($item, $this['global']);
    }
    public function hasPerm($item){
        return in_array($item, $this['detail']);
    }
    
    public function filterPermissions($global,$detail){
        if(!is_array($global))$global=explode(',',(strval($global)));
        if(!is_array($detail))$detail=explode(',',(strval($detail)));
        $globalperms=$this['global'];
        $newglobal=[];
        foreach ($global as $item){
            if(in_array($item,$globalperms)){
                $newglobal[]=$item;
            }
        }
        $detailperms=$this['detail'];
        $newdetail=[];
        foreach ($detail as $item){
            if(in_array($item,$detailperms)){
                $newdetail[]=$item;
            }
        }
        
        return [ implode(',',$newglobal),implode(',',$newdetail) ];
    }
    
}