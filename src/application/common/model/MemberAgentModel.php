<?php

namespace app\common\model;

use app\common\core\CacheableModel;
use think\Db;

/**
 * 会员组
 * Class MemberAgentModel
 * @package app\admin\model
 */
class MemberAgentModel extends CacheableModel
{
    protected $pk = "id";


    public static function init()
    {
        parent::init();
        self::event('after_write', function ($userAgent) {
            if ($userAgent->is_default) {
                Db::name('MemberAgent')->where('id', 'NEQ', $userAgent->id)
                    ->update(array('is_default' => 0));
            }
        });
    }

    protected function get_cache_data()
    {
        $agents = static::order('id ASC')->select()->toArray();
        return array_column($agents, null, 'id');
    }
}
