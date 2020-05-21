<?php

namespace app\common\service;

use app\common\model\WechatModel;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Exceptions\InvalidConfigException;
use EasyWeChat\Kernel\Exceptions\RuntimeException;
use EasyWeChat\Kernel\Messages\Text;
use think\Exception;
use think\facade\Log;

class MessageService{

    protected $wechatApp = null;

    protected static function initWechat(){
        if(is_null($this->wechatApp)){
            $this->wechatApp = WechatModel::createApp();
            if(!$this->wechatApp){
                Log::record('未设置默认微信公众号');
                $this->wechatApp = false;
            }
        }
        return $this->wechatApp;
    }

    /**
     * 发送文本消息或其它类型消息
     * @param string $openid 
     * @param string|Message $message 
     * @param string $link 
     * @return void 
     * @throws Exception 
     * @throws RuntimeException 
     */
    public static function sendWechatMessage($openid, $message, $link = ''){
        $app = $this->initWechat();
        if(!$app) return false;
        if(is_string($message)){
            if(!empty($link)){
                $message .= "【<a href='$link'>查看详情</a>】";
            }
            $message = new Text($message);
        }
        return $app->customer_service->message($message)->to($openid)->send();
    }

    /**
     * 发送模板消息
     * @param mixed $openid 
     * @param mixed $tplid 
     * @param mixed $data 
     * @param string $link 
     * @param mixed|null $miniprogram 
     * @return object|array|string|false 
     * @throws Exception 
     * @throws InvalidArgumentException 
     * @throws InvalidConfigException 
     */
    public static function sendWechatTplMessage($openid, $tplid, $data, $link = '', $miniprogram = null){
        $app = $this->initWechat();
        if(!$app) return false;
        return $app->template_message->send([
            'touser' => $openid,
            'template_id' => $tplid,
            'url' => $link,
            'miniprogram' => $miniprogram,
            'data' =>  $data,
        ]);
    }
}