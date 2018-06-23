<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace app\api\controller;

use sdk\Wechat;
use think\Controller;

class WeChatController extends Controller{
    /**
     * 微信消息接口入口
     * 所有发送到微信的消息都会推送到该操作
     * 所以，微信公众平台后台填写的api地址则为该操作的访问地址
     */
    protected $token;
    protected $appid;
    protected $appsecret;
    protected $encodingaeskey;
    protected $options;
    protected $config=array();

    public function initialize(){
        parent::initialize();
        $this->config=getSettings();

        $this->token = $this->config['token'];
        $this->appid = $this->config['appid'];
        $this->appsecret = $this->config['appsecret'];
        $this->encodingaeskey = $this->config['encodingaeskey'];
        //配置
        $this->options = array(
        'token'=>$this->token, //填写你设定的key
        'encodingaeskey'=>$this->encodingaeskey,//填写加密用的EncodingAESKey，如接口为明文模式可忽略
        'appid'=>$this->appid, //填写高级调用功能的app id
        'appsecret'=>$this->appsecret //填写高级调用功能的密钥
        );
    }
    //微信入口文件
    public function index(){

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
