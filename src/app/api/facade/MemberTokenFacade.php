<?php

namespace app\api\facade;


use think\Facade;

/**
 * Class MemberTokenFacade
 * @package app\api\facade
 * @see \app\api\model\MemberTokenModel
 * @method static array findToken($token)
 * @method static array createToken($member_id, $platform='app', $appid='')
 * @method static array refreshToken($refresh)
 * @method static array clearToken($token)
 */
class MemberTokenFacade extends Facade
{
    protected static function getFacadeClass(){
        return \app\api\model\MemberTokenModel::class;
    }
}