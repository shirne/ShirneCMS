<?php

namespace app\common\model;

use app\common\core\CacheableModel;
use think\facade\Db;

/**
 * 会员组
 * Class MemberLevelModel
 * @package app\admin\model
 */
class MemberLevelModel extends CacheableModel
{
    protected $name = 'member_level';
    protected $pk="level_id";

    protected $type = ['commission_percent'=>'array'];

    public static function onAfterWrite($userLevel)
    {
        if($userLevel->is_default) {
            Db::name('MemberLevel')->where('level_id','<>', $userLevel->level_id)
                ->update(array('is_default' => 0));
        }
    }
    
    protected function get_cache_data()
    {
        $levels=static::order('sort ASC,level_price ASC,level_id ASC')->select()->toArray();
        return array_column($levels,null,'level_id');
    }

}