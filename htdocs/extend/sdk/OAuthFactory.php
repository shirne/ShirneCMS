<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/5/14
 * Time: 11:40
 */

namespace sdk;


use Overtrue\Socialite\SocialiteManager;
use think\Exception;

class OAuthFactory
{
    public static function getInstence($type,$appid,$appkey,$url='')
    {
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
        if($type=='wechat'){
            $driver->scopes(['snsapi_userinfo']);
        }
        return $driver;
    }
}