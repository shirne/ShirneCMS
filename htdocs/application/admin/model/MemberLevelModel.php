<?php

namespace app\admin\model;

use app\common\model\BaseModel;
use think\Db;

/**
 * 会员组
 * Class MemberLevelModel
 * @package app\admin\model
 */
class MemberLevelModel extends BaseModel
{
    protected $pk="level_id";

    protected $type = ['commission_percent'=>'array'];

    public static function init()
    {
        parent::init();
        self::event('after_write',function($userLevel){
            if($userLevel->is_default) {
                Db::name('MemberLevel')->where('level_id','NEQ', $userLevel->level_id)
                    ->update(array('is_default' => 0));
            }
        });
    }

}