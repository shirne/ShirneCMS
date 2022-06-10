<?php
namespace app\common\model;

use app\common\core\BaseModel;
use think\Db;
use think\Exception;

/**
 * Class MemberLoginModel
 * @package app\admin\model
 */
class MemberLoginModel extends BaseModel
{
    protected $autoWriteTimestamp = true;
    public static function createHash($seed){
        do{
            $hash = md5(str_pad($seed, 10,'0',STR_PAD_LEFT) .microtime().mt_rand(10000,99999));
        }while(Db::name('member_login')->where('hash',$hash)->count()>0);
        return $hash;
    }
}