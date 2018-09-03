<?php

namespace app\api\controller;

use app\common\model\OrderModel;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Messages\Message;
use EasyWeChat\Kernel\Messages\Raw;
use sdk\Wechat;
use think\Controller;
use think\Db;
use think\facade\Env;
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
            'aes_key'=>$this->config['encodingaeskey'],
            'app_id'=>$this->config['appid'],
            'secret'=>$this->config['appsecret'],
            'response_type' => 'array',
            'log' => [
                'level' => 'debug',
                'file' => Env::get('runtime_path').'/wechat.log',
            ],
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
            $this->options['aes_key']=$account['encodingaeskey'];
            $this->options['app_id']=$account['appid'];
            $this->options['secret']=$account['appsecret'];
        }

        $app = Factory::officialAccount($this->options);
        $app->server->push(function ($message) {
            switch ($message['MsgType']) {
                case 'event':
                    switch ($message['event']){
                        case 'subscribe':
                            return '谢谢关注!';
                            break;
                        case 'SCAN':
                            return '已关注扫码';
                            break;
                        case 'LOCATION':
                            return '位置上报事件';
                            break;
                        case 'CLICK':
                            return '菜单点击事件';
                            break;
                        case 'VIEW':
                            return '链接点击事件';
                            break;
                        case 'TEMPLATESENDJOBFINISH':
                            $this->updateTplMsg($message);
                            break;
                        default:
                            return '收到事件消息';
                    }
                    break;
                case 'text':
                    return '收到文字消息';
                    break;
                case 'image':
                    return '收到图片消息';
                    break;
                case 'voice':
                    return '收到语音消息';
                    break;
                case 'video':
                    return '收到视频消息';
                    break;
                case 'location':
                    return '收到坐标消息';
                    break;
                case 'link':
                    return '收到链接消息';
                    break;
                case 'file':
                    return '收到文件消息';
                // ... 其它消息
                default:
                    return '收到其它消息';
                    break;
            }

            return new Raw('');
        });

        $response = $app->server->serve();

        $response->send();
    }

    private function updateTplMsg($message){
        $result=$message['Status'];
        $msgid=$message['MsgID'];
        if($result=='success') {
            Db::name('taskTemplate')->where('msgid', $msgid)->update([
                'is_send'=>2,
                'send_result'=>$result
            ]);
        }else{
            Db::name('taskTemplate')->where('msgid', $msgid)->update([
                'is_send'=>-2,
                'send_result'=>$result
            ]);
        }
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

    private function checkClick($message)
    {
    }
}
