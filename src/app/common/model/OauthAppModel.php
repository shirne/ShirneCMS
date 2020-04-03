<?php


namespace app\common\model;


use app\common\core\BaseModel;
use think\facade\Db;
use think\Exception;

class OauthAppModel extends BaseModel
{
    protected $name = 'oauth_app';
    /**
     * 验证签名
     * @param array $data 必须包含appid,timestamp和sign
     * @throws Exception
     * @return bool|array
     */
    public static function checkSign($data){
        if(empty($data['appid']) || empty($data['sign'])){
            return false;
        }
        if(empty($data['timestamp']) || abs($data['timestamp']-time())>600){
            return false;
        }
        $app = Db::name('oauthApp')->where('appid',$data['appid'])->find();
        if(empty($app)){
            return false;
        }
        $sign = $data['sign'];
        $mysign = md5(self::joinData($data).'&'.$app['appsecret']);
        if($sign === $mysign){
            return $app;
        }
        return false;
    }
    protected static function joinData($data){
        $join=[];
        ksort($data);
        foreach ($data as $key=>$item){
            if($key != 'sign'){
                $join[]="{$key}={$item}";
            }
        }
        return implode('&',$join);
    }
}