<?php

namespace sdk;

use Overtrue\Socialite\SocialiteManager;
use think\Exception;

/**
 * Class OAuthFactory
 * @package sdk
 */
class OAuthFactory
{
    public static function getInstence($type,$appid,$appkey,$url='',$iswechat=false)
    {
        if($type=='wechat')$iswechat=true;
        if($type=='wechat_open')$type='wechat';
        $config=[
            $type=>[
                'client_id' => $appid,
                'client_secret' => $appkey,
                'redirect' => $url,
            ],
            'guzzle'=>[
                'verify'=>false
            ]
        ];
        $factory=new SocialiteManager($config);
        $driver = $factory->driver($type);
        if($iswechat){
            $driver->scopes(['snsapi_userinfo']);
        }
        return $driver;
    }
}