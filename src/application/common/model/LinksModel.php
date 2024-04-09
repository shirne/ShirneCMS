<?php

namespace app\common\model;


use app\common\core\BaseModel;
use think\Db;

/**
 * Class LinksModel
 * @package app\admin\model
 */
class LinksModel extends BaseModel
{
    protected $autoWriteTimestamp = true;

    protected static $link_groups = [];
    public static function getGroups()
    {
        return self::$link_groups;
    }
}
