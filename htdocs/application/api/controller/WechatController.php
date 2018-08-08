<?php

namespace app\api\controller;

use app\common\model\OrderModel;
use EasyWeChat\Factory;
use sdk\Wechat;
use think\Controller;
use think\Db;
use think\facade\Log;

/**
 * 微信消息接口入口
 * Class WeChatController
 * @package app\api\controller
 */
class WechatController extends Controller{

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

    public function payresult(){
        $config = [
            'app_id'             => $this->config['appid'],
            'mch_id'             => $this->config['mch_id'],
            'key'                => $this->config['key'],

            'cert_path'          => $this->config['cert_path'],
            'key_path'           => $this->config['key_path']
        ];

        $app = Factory::payment($config);

        $response = $app->handlePaidNotify(function ($message, $fail) {
            // 记录日志
            Log::write(var_export($message,TRUE),'pay');

            $model=new OrderModel();
            $order = $model->where('order_no',$message['out_trade_no'])->find();

            if (empty($order) || $order['pay_time']>0) {
                return true;
            }


            if ($message['return_code'] === 'SUCCESS') {
                // 用户是否支付成功
                if ($message['result_code'] === 'SUCCESS') {
                    $order['pay_time'] = time();
                    $order['status']= 1;

                    // 用户支付失败
                } elseif ($message['result_code'] === 'FAIL') {
                    $order['status'] = 0;
                }
            } else {
                return $fail('通信失败，请稍后再通知我');
            }

            $order->save();

            return true;
        });

        $response->send();
    }
    public function scanpay(){
        $config = [
            'app_id'             => $this->config['appid'],
            'mch_id'             => $this->config['mch_id'],
            'key'                => $this->config['key'],

            'cert_path'          => $this->config['cert_path'],
            'key_path'           => $this->config['key_path']
        ];

        $app = Factory::payment($config);

        $response = $app->handlePaidNotify(function ($message, $fail) {
            // 记录日志
            Log::write(var_export($message,TRUE),'pay');

            return true;
        });

        $response->send();
    }
}
