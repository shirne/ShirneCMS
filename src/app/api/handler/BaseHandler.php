<?php


namespace app\api\handler;


use app\api\processor\BaseProcessor;
use app\common\model\MemberOauthModel;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\Media;
use EasyWeChat\Kernel\Messages\Message;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\ServiceContainer;
use think\facade\Db;
use think\facade\Log;

class BaseHandler
{
    /**
     * @var ServiceContainer
     */
    protected $app;

    protected $account;
    protected $account_id;
    protected $account_type;

    protected $user;

    public function __get($name)
    {
        if(property_exists($this,$name)){
            return $this->$name;
        }
        return null;
    }

    public function __construct($app = null)
    {
        $this->app = $app;
        $this->account = $this->app['account'];
        $this->account_id = $this->account['id'];
        $this->account_type = $this->account['account_type'];
    }

    protected function onSubscribe($message, $userInfo)
    {

        $this->user = MemberOauthModel::checkUser($userInfo, $this->account);
        if (!$this->user['is_new']) {
            return $this->getTypeReply('resubscribe');
        }

        return $this->getTypeReply('subscribe');
    }

    protected function onUnSubscribe($message)
    {
        $openid = $message['FromUserName'];
        $userinfo = MemberOauthModel::where('openid',$openid)->find();
        if(!empty($userinfo)){
            $userinfo->save(['is_follow'=>0]);
        }

        return '';
    }

    protected function onScan($message, $scene_id)
    {
        return $this->getTypeReply('scan', $scene_id);
    }

    protected function onLocation($message)
    {
        return $this->getTypeReply('location');
    }
    protected function onClick($message)
    {
        return $this->getTypeReply('click', $message['EventKey']);
    }

    protected function onView($message)
    {
        return '';
    }

    /**
     * 匹配回复消息
     * @param $type
     * @param string $key
     * @return Image|Message|News|string
     */
    protected function getTypeReply($type, $key = '')
    {
        $model = Db::name('WechatReply')
            ->where('wechat_id', $this->account_id)
            ->where('type', $type);
        if (!empty($key)) {
            $model->where('keyword', $key);
        }
        try {
            $result = $model->find();
        } catch (\Exception $e) {
        }

        if (empty($result)) {
            if ($type == 'resubscribe') {
                return $this->getTypeReply('subscribe');
            } else {
                return "";
            }
        }
        return $this->getReply($result);
    }

    protected function replace_user_info($matches){
        $key = $matches[1];
        if(isset($this->user[$key])){
            if($key == 'sex'){
                return $this->user['gender']==1?'先生':($this->user['gender']==2?'女士':'');
            }
            return $this->user[$key];
        }
        return '';
    }

    protected function getReply($reply)
    {
        switch ($reply['reply_type']) {
            case 'text':
                $content = $reply['content'];
                $content = preg_replace_callback('/\[(\w+)\]/',array($this, 'replace_user_info'),$content);
                return $content;
                break;
            case 'news':
                $news = json_decode($reply['content'], TRUE);
                $items = [];
                foreach ($news as $k => $new) {
                    $new['image'] = local_media($new['image']);
                    $items[] = new NewsItem($new);
                    break; // 只能对回复一条
                }
                return new News($items);
                break;
            case "image":
                $content = json_decode($reply['content'], TRUE);
                $media_id = $content['media_id'];
                if (!$media_id || $content['last_time'] < time() - 60 * 60 * 24 * 3) {
                    $media = $this->app->media->uploadImage(DOC_ROOT . $content['image']);
                    if (empty($media['media_id'])) {
                        return '素材上传失败';
                    }
                    $media_id = $media['media_id'];
                    $content['media_id'] = $media_id;
                    $content['last_time'] = time();
                    Db::name('WechatReply')
                        ->where('id', $reply['id'])
                        ->update([
                            'content' => json_encode($content)
                        ]);
                }
                return new Image($media_id);
            case "custom":
                $config = json_decode($reply['content'], TRUE);
                $processor = $config['processor'];
                if ($processor) {
                    $processor = BaseProcessor::factory($processor, $this->app, $this);
                    return $processor->process($config);
                }
                Log::record($reply['content']);
                return 'error';
            default:
                return $reply['content'];
        }
    }

    protected function updateTplMsg($message)
    {
        $result = $message['Status'];
        $msgid = $message['MsgID'];
        if ($result == 'success') {
            Db::name('taskTemplate')->where('msgid', $msgid)->update([
                'status' => 2,
                'send_result' => $result
            ]);
        } else {
            Db::name('taskTemplate')->where('msgid', $msgid)->update([
                'status' => -2,
                'send_result' => $result
            ]);
        }
    }
}
