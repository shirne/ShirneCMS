<?php


namespace app\common\model;


use app\common\core\BaseModel;

class NoticeModel extends BaseModel
{
    protected $autoWriteTimestamp = true;
    protected static $flags = [];

    public static function getFlags()
    {
        return static::$flags;
    }
}
