<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/5/14
 * Time: 11:40
 */

namespace sdk;


use think\Exception;

class OAuthFactory
{
    public static function getInstence($type,$appid,$appkey,$url='')
    {
        switch ($type){
            case 'qq':
                $instence= new \Yurun\OAuthLogin\QQ\OAuth2($appid,$appkey,$url);
                break;
            case 'weixin':
                $instence= new \Yurun\OAuthLogin\Weixin\OAuth2($appid,$appkey,$url);
                $instence->scope='snsapi_userinfo';
                break;
            case 'weibo':
                $instence= new \Yurun\OAuthLogin\Weibo\OAuth2($appid,$appkey,$url);
                break;
            case 'baidu':
                $instence= new \Yurun\OAuthLogin\Baidu\OAuth2($appid,$appkey,$url);
                break;
            case 'gitee':
                $instence= new \Yurun\OAuthLogin\Gitee\OAuth2($appid,$appkey,$url);
                break;
            case 'csdn':
                $instence= new \Yurun\OAuthLogin\CSDN\OAuth2($appid,$appkey,$url);
                break;
            case 'coding':
                $instence= new \Yurun\OAuthLogin\Coding\OAuth2($appid,$appkey,$url);
                break;
            case 'oschina':
                $instence= new \Yurun\OAuthLogin\OSChina\OAuth2($appid,$appkey,$url);
                break;
            default:
                throw new Exception('不支持的第三方授权');
        }
        return $instence;
    }
}