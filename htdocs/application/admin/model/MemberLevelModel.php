<?php
/**
 * 会员组
 * User: shirne
 * Date: 2018/4/11
 * Time: 9:01
 */

namespace app\admin\model;

use think\Db;
use think\Model;

class MemberLevelModel extends Model{
    protected $pk="level_id";

    protected $json = ['commission_percent'];

    public static function init()
    {
        parent::init();
        self::event('after_write','_set_default');
    }

    protected static function _set_default($userLevel){
        if($userLevel->is_default) {
            Db::name('MemberLevel')->where('level_id','NEQ', $userLevel->level_id)
                ->update(array('is_default' => 0));
        }
    }
}