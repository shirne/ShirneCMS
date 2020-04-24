<?php

namespace app\common\model;


use app\common\core\BaseModel;
use think\facade\Db;

/**
 * Class LinksModel
 * @package app\admin\model
 */
class LinksModel extends BaseModel
{
    protected $name = 'links';
    protected $autoWriteTimestamp = true;
    
    protected static $link_groups=[];
    public static function getGroups(){
        if(is_null(self::$link_groups)){
            $group = Db::name('Links')->where('group','<>','')->distinct(true)->field('group')->select();
            self::$link_groups = array_column($group->all(),'group');
        }
        return self::$link_groups;
    }
}