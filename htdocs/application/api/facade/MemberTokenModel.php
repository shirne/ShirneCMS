<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/5/12
 * Time: 23:11
 */

namespace app\api\facade;


use think\Facade;

/**
 * Class MemberTokenModel
 * @package app\api\facade
 * @see \app\api\model\MemberTokenModel
 * @method array findToken($token) static
 * @method array createToken($member_id) static
 * @method array refreshToken($refresh) static
 */
class MemberTokenModel extends Facade
{
    protected static function getFacadeClass(){
        return \app\api\model\MemberTokenModel::class;
    }
}