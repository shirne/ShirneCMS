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
                'identifier' => $appid,
                'secret' => $appkey,
                'callback_uri' => $url,
            ],
            'guzzle'=>[
                'verify'=>false
            ]
        ];
        $factory=new SocialiteManager($config);
        return $factory->driver($type);
    }
}