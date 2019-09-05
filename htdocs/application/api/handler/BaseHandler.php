<?php


namespace app\api\handler;


use app\api\processer\BaseProcesser;
use app\common\model\MemberOauthModel;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\Media;
use EasyWeChat\Kernel\Messages\Message;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\ServiceContainer;
use think\Db;

class BaseHandler
{
    /**
     * @var ServiceContainer
     */
    protected $app;
    
    protected $account;
    protected $account_id;
    protected $account_type;
    
    public function __construct($app=null)
    {
        $this->app = $app;
        $this->account = $this->app['account'];
        $this->account_id = $this->account['id'];
        $this->account_type = $this->account['account_type'];
    }
    
    protected function onSubscribe($message,$userInfo){
        $user=Db::name('memberOauth')->where('type_id',$this->account_id)
            ->where('type',$this->account_type)
            ->where('openid',$message['FromUserName'])->find();
        
        $data=MemberOauthModel::mapUserInfo($userInfo);
        $data['is_follow']=1;
        //可能是首次订阅
        if(empty($user)){
            $data['member_id']=0;
            $data['email'] ='';
            $data['type'] = $this->account_type;
            $data['type_id'] = $this->account_id;
            MemberOauthModel::create($data);
            Db::name('memberOauth')->where('type_id',$this->account_id)
                ->where('type',$this->account_type)
                ->where('openid',$message['FromUserName'])->find();
            
            return $this->getTypeReply('resubscribe');
        }
        Db::name('memberOauth')->where('type_id',$this->account_id)
            ->where('type',$this->account_type)
            ->where('openid',$message['FromUserName'])->update($data);
        
        return $this->getTypeReply('subscribe');
    }
    
    protected function onScan($message,$scene_id){
        return $this->getTypeReply('scan',$scene_id);
    }
    
    protected function onLocation($message){
        return $this->getTypeReply('location');
    }
    protected function onClick($message){
        return $this->getTypeReply('click',$message['EventKey']);
    }
    
    protected function onView($message){
        return '';
    }
    
    /**
     * 匹配回复消息
     * @param $type
     * @param string $key
     * @return Image|Message|News|string
     */
    protected function getTypeReply($type, $key=''){
        $model=Db::name('WechatReply')
            ->where('wechat_id',$this->account_id)
            ->where('type',$type);
        if(!empty($key)){
            $model->where('keyword',$key);
        }
        try {
            $result = $model->find();
        }catch(\Exception $e){}
        
        if(empty($result)) {
            if ($type == 'resubscribe') {
                return $this->getTypeReply('subscribe');
            }else{
                return "";
            }
        }
        return $this->getReply($result);
    }
    
    protected function getReply($reply){
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
    
    protected function updateTplMsg($message){
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
}