<?php

namespace app\api\controller;

use sdk\Wechat;
use think\Controller;
use think\Db;
use think\facade\Log;

/**
 * 微信消息接口入口
 * Class WeChatController
 * @package app\api\controller
 */
class WeChatController extends Controller{

    protected $options;
    protected $config=array();

    public function initialize(){
        parent::initialize();
        $this->config=getSettings();

        //配置
        $this->options = array(
            'token'=>$this->config['token'],
            'encodingaeskey'=>$this->config['encodingaeskey'],
            'appid'=>$this->config['appid'],
            'appsecret'=>$this->config['appsecret'],
            'debug'=>true,
            'logcallback'=>function($log){
                Log::write($log,'Wechat');
            }
        );
    }

    //微信入口文件
    public function index($hash=''){
        if(!empty($hash)){
            $account=Db::name('wechat')->where('hash',$hash)->find();
            if(empty($account)){
                Log::write('公众号['.$hash.']不存在','Wechat');
            }
            $this->options['token']=$account['token'];
            $this->options['encodingaeskey']=$account['encodingaeskey'];
            $this->options['appid']=$account['appid'];
            $this->options['appsecret']=$account['appsecret'];
        }



        $wechat = new Wechat($this->options);
        $wechat->valid();
        $type = $wechat->getRev()->getRevType();
        switch($type) {
            case Wechat::MSGTYPE_TEXT:
                $url = url('login/wechatCallback','',true,true);
                $redirect = $wechat->getOauthRedirect($url);
                $wechat->text("<a href=\"$redirect\">点击登陆</a>")->reply();
                break;
            case Wechat::MSGTYPE_LOCATION:
                $localtion = $wechat->getRevGeo();
                $wechat->text(json_encode($localtion))->reply();
                break;
            case Wechat::EVENT_SUBSCRIBE:
                $wechat->text("谢谢关注")->reply();
                break;
            case Wechat::MSGTYPE_IMAGE:
                $wechat->text("...")->reply();
                break;
            case Wechat::EVENT_SCAN:
                //扫码
                break;
            case Wechat::EVENT_LOCATION:
                //上报位置
                break;
            case Wechat::EVENT_MENU_CLICK:
                //菜单点击
                break;
            default:
                $wechat->text("Hello!")->reply();
        }
        return '';
    }
}
