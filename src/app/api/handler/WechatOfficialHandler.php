<?php


namespace app\api\handler;


use EasyWeChat\Kernel\Contracts\EventHandlerInterface;
use EasyWeChat\Kernel\Messages\Raw;
use app\common\model\MemberOauthModel;
use think\facade\Log;

class WechatOfficialHandler extends BaseHandler implements EventHandlerInterface
{
    /**
     * @param mixed $message
     */
    public function handle($message = null)
    {
        Log::info('接收到消息:'.var_export($message,true),'Wechat');
        if(empty($message) || !isset($message['MsgType'])){
            return '';
        }
        
        //非订阅事件自动处理会员
        if($message['MsgType'] != 'event' || $message['Event']!= 'subscribe'){
            $hasUser = MemberOauthModel::where('openid',$message['FromUserName'])->find();
            if(empty($hasUser) || empty($hasUser['member_id'])){
                $userinfo=$this->app->user->get($message['FromUserName']);
                $this->user = MemberOauthModel::checkUser($userinfo, $this->account);
            }else{
                $this->user = $hasUser;
            }
        }

        switch ($message['MsgType']) {
            case 'event':
                switch ($message['Event']){
                    case 'subscribe':
                        $userinfo=$this->app->user->get($message['FromUserName']);
                        if(!empty($message['EventKey'])){
                            $subscribe = $this->onSubscribe($message, $userinfo);
                            $scene_id=substr($message['EventKey'],8);
                            return $this->onScan($message,$scene_id)?:$subscribe;
                        }else {
                            return $this->onSubscribe($message, $userinfo);
                        }
                        break;
                    case 'unsubscribe':
                        return $this->onUnSubscribe($message);
                        break;
                    case 'SCAN':
                        return $this->onScan($message,$message['EventKey']);
                        break;
                    case 'scancode_waitmsg':
                        break;
                    case 'scancode_push':
                        break;
                    case 'LOCATION':
                        return $this->onLocation($message);
                        break;
                    case 'location_select':
                        return $this->onLocation($message);
                        break;
                    case 'CLICK':
                        return $this->onClick($message);
                        break;
                    case 'VIEW':
                        return $this->onView($message);
                        break;
                    case 'view_miniprogram':
                        return $this->onView($message);
                        break;
                    case 'TEMPLATESENDJOBFINISH':
                        $this->updateTplMsg($message);
                        break;
                    case 'pic_weixin':
                        break;
                    case 'pic_photo_or_album':
                        break;
                    case 'pic_sysphoto':
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
    }
}