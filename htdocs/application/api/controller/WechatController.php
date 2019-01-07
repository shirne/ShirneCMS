<?php

namespace app\api\controller;

use app\api\Processer\BaseProcesser;
use app\common\model\MemberOauthModel;
use app\common\model\OrderModel;
use app\common\model\PayOrderModel;
use app\common\model\WechatModel;
use EasyWeChat\BasicService\Application;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\Media;
use EasyWeChat\Kernel\Messages\Message;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\Raw;
use sdk\Wechat;
use think\Controller;
use think\Db;
use think\Exception;
use think\facade\Env;
use think\facade\Log;

/**
 * 微信消息接口入口
 * Class WeChatController
 * @package app\api\controller
 */
class WechatController extends Controller{

    protected $login_type='wechat';

    protected $options;
    protected $type='wechat';
    protected $config=array();
    protected $account_id=0;

    /**
     * @var Application
     */
    protected $app=null;

    public function initialize(){
        parent::initialize();
        $this->config=getSettings();
    }

    protected function getAccount($hash=''){
        if(!empty($hash)){
            $account=Db::name('wechat')->where('hash',$hash)->find();
            if(empty($account)){
                Log::record('公众号['.$hash.']不存在','Wechat');
            }
        }else{
            $account=Db::name('wechat')->where('is_default',1)->where('type','wechat')->find();
            if(empty($account)){
                Log::record('没有设置默认公众号','Wechat');
            }
        }
        if(empty($account)){
            throw new Exception('公众号设置错误');
        }
        return $account;
    }

    //微信入口文件
    public function index($hash=''){
        $account=$this->getAccount($hash);
        $this->type = $account['account_type'];
        $this->account_id=$account['id'];

        $this->options=WechatModel::to_config($account);

        switch ($this->type) {
            case 'wechat':
            case 'subscribe':
            case 'service':
                $this->app=$this->servOfficial();
                break;
            case 'miniprogram':
            case 'minigame':
                $this->app=$this->servMini();
                break;
            default:
                exit('配置错误');
                break;
        }

        $response = $this->app->server->serve();

        $response->send();
        exit;
    }

    private function servOfficial(){

        $app = Factory::officialAccount($this->options);
        $app->server->push(function ($message) use ($app) {
            switch ($message['MsgType']) {
                case 'event':
                    switch ($message['event']){
                        case 'subscribe':
                            $userinfo=$app->user->get($message['FromUserName']);
                            if(!empty($message['EventKey'])){
                                $this->onSubscribe($message, $userinfo);
                                $scene_id=substr($message['EventKey'],8);
                                return $this->onScan($message,$scene_id);
                            }else {
                                return $this->onSubscribe($message, $userinfo);
                            }
                            break;
                        case 'SCAN':
                            return $this->onScan($message,$message['EventKey']);
                            break;
                        case 'LOCATION':
                            return $this->onLocation($message);
                            break;
                        case 'CLICK':
                            return $this->onClick($message);
                            break;
                        case 'VIEW':
                            return $this->onView($message);
                            break;
                        case 'TEMPLATESENDJOBFINISH':
                            $this->updateTplMsg($message);
                            break;
                        default:
                            return '收到事件消息';
                    }
                    break;
                case 'text':
                    return $this->getTypeReply('keyword',$message['Content']);
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

        return $app;
    }

    private function servMini(){

        $app = Factory::miniProgram($this->options);
        $app->server->push(function ($message) use ($app) {

        });
        return $app;
    }

    private function onSubscribe($message,$userInfo){
        $user=Db::name('memberOauth')->where('type_id',$this->account_id)
            ->where('type',$this->login_type)
            ->where('openid',$message['FromUserName'])->find();

        $data=MemberOauthModel::mapUserInfo($userInfo);
        $data['is_follow']=1;
        //可能是首次订阅
        if(empty($user)){
            $data['member_id']=0;
            $data['email'] ='';
            $data['type'] = $this->login_type;
            $data['type_id'] = $this->account_id;
            MemberOauthModel::create($data);
            Db::name('memberOauth')->where('type_id',$this->account_id)
                ->where('type',$this->login_type)
                ->where('openid',$message['FromUserName'])->find();

            return $this->getTypeReply('resubscribe');
        }
        Db::name('memberOauth')->where('type_id',$this->account_id)
            ->where('type',$this->login_type)
            ->where('openid',$message['FromUserName'])->update($data);

        return $this->getTypeReply('subscribe');
    }

    private function onScan($message,$scene_id){
        return $this->getTypeReply('scan',$scene_id);
    }

    private function onLocation($message){
        return $this->getTypeReply('location');
    }
    private function onClick($message){
        return $this->getTypeReply('click',$message['EventKey']);
    }

    private function onView($message){

    }
    private function getTypeReply($type, $key=''){
        $model=Db::name('WechatReply')
            ->where('wechat_id',$this->account_id)
            ->where('type',$type);
        if(!empty($key)){
            $model->where('keyword',$key);
        }
        $result=$model->find();
        if(empty($result)) {
            if ($type == 'resubscribe') {
                return $this->getTypeReply('subscribe');
            }else{
                return "";
            }
        }
        return $this->getReply($result);
    }

    private function getReply($reply){
        switch ($reply['reply_type']){
            case 'text':
                return $reply['content'];
                break;
            case 'news':
                return new News(json_decode($reply['content'],TRUE));
                break;
            case "image":
                $content=json_decode($reply['content'],TRUE);
                $media_id=$content['media_id'];
                if(!$media_id || $content['last_time']<time()-60*60*24*3){
                    $media=$this->app->media->uploadImage(DOC_ROOT.$content['image']);
                    if(empty($media['media_id'])){
                        return '素材上传失败';
                    }
                    $media_id=$media['media_id'];
                    $content['media_id']=$media_id;
                    $content['last_time']=time();
                    Db::name('WechatReply')
                        ->where('id',$reply['id'])
                        ->update([
                            'content'=>json_encode($content)
                        ]);
                }
                return new Image(new Media($media_id));
            case "custom":
                $config=json_decode($reply['content'],TRUE);
                $processer=$config['processer'];
                if($processer){
                    $processer=BaseProcesser::factory($processer,$this->app);
                    return $processer->process($config);
                }
                return 'error';
            default:
                return $reply['content'];
        }
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
    public function payresult($hash=''){
        $account=$this->getAccount($hash);
        $config = WechatModel::to_pay_config($account);

        $app = Factory::payment($config);

        $response = $app->handlePaidNotify(function ($message, $fail) {
            // 记录日志
            Log::record(var_export($message,TRUE),'pay');

            /**
             * @var $order PayOrderModel
             */
            $order = PayOrderModel::where(['order_no'=>$message['out_trade_no']])->find();

            if (empty($order) || $order['pay_time']>0) {
                return true;
            }


            if ($message['return_code'] === 'SUCCESS') {
                // 用户是否支付成功
                if ($message['result_code'] === 'SUCCESS') {
                    $data = [
                        'status'=>1,
                        'pay_time'=>time(),
                        'pay_bill'=>$message['transaction_id'],
                        'time_end'=>$message['time_end']
                    ];

                    // 用户支付失败
                } elseif ($message['result_code'] === 'FAIL') {
                    $data = [
                        'status'=>0
                    ];
                }
            } else {
                return $fail('通信失败，请稍后再通知我');
            }

            if(!empty($data)){
                $order->updateStatus($data);
            }

            return true;
        });

        $response->send();
    }
    public function scanpay($hash=''){
        $account=$this->getAccount($hash);
        $config = WechatModel::to_pay_config($account);

        $app = Factory::payment($config);

        $response = $app->handlePaidNotify(function ($message, $fail) {
            // 记录日志
            Log::record(var_export($message,TRUE),'pay');

            return true;
        });

        $response->send();
    }
}
