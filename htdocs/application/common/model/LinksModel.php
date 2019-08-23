<?php

namespace app\common\model;


use think\Db;

/**
 * Class LinksModel
 * @package app\admin\model
 */
class LinksModel extends BaseModel
{
    protected $autoWriteTimestamp = true;
    
    protected static $link_groups=[];
    public static function getGroups(){
        if(is_null(self::$link_groups)){
            $group = Db::name('Links')->where('group','<>','')->distinct(true)->field('group')->select();
            self::$link_groups = array_column($group,'group');
        }
        return self::$link_groups;
    }
}