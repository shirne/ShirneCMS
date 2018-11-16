<?php

namespace app\common\model;

/**
 * Class WechatModel
 * @package app\common\model
 */
class WechatModel extends BaseModel
{
    protected $autoWriteTimestamp = true;

    public static function to_config($data){
        $options=[
            'response_type' => 'array',
            'log' => config('log.wechat'),
        ];
        $options['token']=$data['token'];
        $options['aes_key']=$data['encodingaeskey'];
        $options['app_id']=$data['appid'];
        $options['secret']=$data['appsecret'];
        return $options;
    }

    public function toConfig(){
        return self::to_config($this);
    }
}