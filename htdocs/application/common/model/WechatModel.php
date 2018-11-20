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

    public static function to_pay_config($data,$notify='', $useCert=false){

        // 必要配置
        $config = [
            'app_id'             => $data['appid'],
            'mch_id'             => $data['mch_id'],
            'key'                => $data['key'],
        ];

        // 如需使用敏感接口（如退款、发送红包等）需要配置 API 证书路径(登录商户平台下载 API 证书)
        if($useCert){
            $config['cert_path'] = $data['cert_path'];
            $config['key_path']  = $data['key_path'];
        }

        if($notify){
            $config['notify_url']=$notify;
        }
        return $config;
    }

    public function toPayConfig(){
        return self::to_pay_config($this);
    }
}