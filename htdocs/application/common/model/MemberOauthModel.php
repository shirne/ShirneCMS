<?php

namespace app\common\model;


use app\common\core\BaseModel;

/**
 * Class MemberOauthModel
 * @package app\common\model
 */
class MemberOauthModel extends BaseModel
{
    protected $autoWriteTimestamp = true;

    public static function mapUserInfo($userInfo){
        $data=[];

        $data['openid'] = $userInfo['openid'];
        $data['nickname'] =$userInfo['nickname'];
        $data['name'] =$userInfo['nickname'];
        $data['avatar'] =$userInfo['headimgurl'];

        $data['gender'] = empty($userInfo['gender'])?0:$userInfo['gender'];
        $data['unionid'] = empty($userInfo['unionid'])?'':$userInfo['unionid'];
        $data['data']=json_encode($userInfo);

        $data['city'] =$userInfo['city'];
        $data['province'] =$userInfo['province'];
        $data['country'] =$userInfo['country'];
        $data['language'] =$userInfo['language'];
        $data['subscribe_time']=$userInfo['subscribe_time'];

        return $data;
    }
}