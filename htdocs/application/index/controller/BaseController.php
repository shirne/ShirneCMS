<?php
namespace app\index\controller;

use app\admin\model\MemberLevelModel;
use EasyWeChat\Factory;
use extcore\traits\Email;
use think\Controller;
use think\Db;
use think\facade\Env;

/**
 * 如果某个控制器必须用户登录才可以访问  
 * 请继承该控制器
 */
class BaseController extends Controller
{
    use Email;

    protected $userid;
    protected $user;
    protected $openid;
    protected $wechatUser;
    protected $userLevel;
    protected $isLogin=false;
    protected $errMsg;
    protected $config=array();
    protected $isWechat=false;
    protected $isMobile=false;

    public function initialize(){
        parent::initialize();

        $this->config=getSettings();
        $this->assign('config',$this->config);

        $navigation=config('navigator.');
        $navigation=parseNavigator($navigation,$this->request->module());
        $this->assign('navigator',$navigation);
        $this->assign('navmodel','index');

        $this->checkPlatform();

        $this->checkLogin();

        $this->assign('isLogin',$this->isLogin);
        $this->assign('protocol',$this->request->scheme());

        $this->seo();
    }

    public function seo($title='',$keys='',$desc=''){
        $sitename=$this->config['site-name'];
        if(empty($title)){
            $title .= $sitename;
        }elseif($title!=$sitename){
            $title .= ' - '.$sitename;
        }
        if(empty($keys)){
            $keys = $this->config['site-keywords'];
        }
        if(empty($desc)){
            $desc = $this->config['site-description'];
        }

        $this->assign('title',$title);
        $this->assign('keywords',$keys);
        $this->assign('description',$desc);
    }

    public function checkLogin(){
        $this->userid = session('userid');
        if(!empty($this->userid)){
            $this->user = Db::name('Member')->find($this->userid);
            /*$time=session('logintime');
            if($time != $this->user['logintime']){
                session('userid',null);
                $this->error('您的帐号已在其它地区登录！');
            }*/
            if(!empty($this->user)) {
                $this->isLogin=true;
                $this->assign('user', $this->user);
            }else{
                $this->userid=null;
                clearLogin(false);
                $this->error("登录失效",url('index/login/index'));
            }
        }

        if($this->wechatLogin() && $this->config['wechat_autologin']=='1' ){
            redirect()->remember();
            redirect(url('index/login/index',['type'=>'wechat']))->send();exit;
            //$this->wechatLogin();
        }
        $this->assign('wechatUser', $this->wechatUser);
    }
    protected function wechatLogin(){
        if(!$this->isWechat){
            $this->errMsg='非微信内部浏览器';
            return false;
        }
        $agree=session('wechat_agree');
        if($agree=='2'){
            $this->errMsg='用户拒绝授权';
            return false;
        }

        //跳过登录页面
        if(strtolower($this->request->controller())=='login' &&
            (strtolower($this->request->action())=='index' || strtolower($this->request->action())=='callback')
        ){
            if(!empty($openid)){
                $wechatUser=Db::name('memberOauth')->where('openid',$openid)->find();
                if($wechatUser['member_id']){
                    $member=MemberModel::get($wechatUser['member_id']);
                    if(!empty($member)) {
                        setLogin($member);

                        redirect()->restore()->send();exit;
                    }
                }
                $this->wechatUser=$wechatUser;
                return false;
            }
            return false;
        }

        $this->openid=$openid=session('openid');

        if($this->isLogin){
            if(empty($openid)) {
                $wechatUser = Db::name('memberOauth')
                    ->where('member_id', $this->userid)
                    ->where('type_id', 0)
                    ->where('type', 'wechat')
                    ->find();
                if (!empty($wechatUser)) {
                    $this->wechatUser = $wechatUser;
                    session('openid',$this->wechatUser['openid']);
                    return false;
                }
            }else{
                $this->wechatUser=Db::name('memberOauth')->where('openid',$openid)->find();
                return false;
            }
        }else{
            if(!empty($openid)){
                $wechatUser=Db::name('memberOauth')->where('openid',$openid)->find();
                if($wechatUser['member_id']){
                    $member=MemberModel::get($wechatUser['member_id']);
                    if(!empty($member)) {
                        setLogin($member);

                        redirect()->restore()->send();exit;
                    }
                }
                $this->wechatUser=$wechatUser;
                return false;
            }
        }
        return true;
    }
    public function initLevel(){
        if($this->isLogin && empty($this->userLevel)){
            $this->userLevel=MemberLevelModel::get($this->user['level_id']);
        }
    }
    public function checkPlatform(){
        $detected=session('detected');
        if(empty($detected)) {
            $useragent = $this->request->server('HTTP_USER_AGENT');
            if (stripos($useragent, 'MicroMessenger') > 0) {
                $this->isWechat = true;
                $this->isMobile = true;
            }else {
                $this->isMobile = $this->request->isMobile();
            }
            session('detected',1);
            session('isWechat',$this->isWechat);
            session('isMobile',$this->isMobile);
        }else{
            $this->isWechat = session('isWechat');
            $this->isMobile = session('isMobile');
        }
        $this->assign('isWechat',$this->isWechat);
        $this->assign('isMobile',$this->isMobile);

        if(config('template.independence')){
            $base_path=config('template.view_path');
            if($this->isMobile){
                $this->view->config('view_path', $base_path.'mobile'.DIRECTORY_SEPARATOR);
            }else{
                $this->view->config('view_path', $base_path.'default'.DIRECTORY_SEPARATOR);
            }
        }

        /**
         * 微信JSSDK
         * 详细用法参考：http://mp.weixin.qq.com/wiki/7/1c97470084b73f8e224fe6d9bab1625b.html
         */
        if($this->isWechat && !empty($this->config['appid'])) {
            $app = Factory::officialAccount([
                'app_id' => $this->config['appid'],
                'secret' => $this->config['appsecret'],
                'token' => $this->config['token'],
                'response_type' => 'array',
                'log' => [
                    'level' => 'debug',
                    'file' => Env::get('runtime_path').'/wechat.log',
                ],
            ]);
            $signPackage=$app->jssdk->buildConfig([
                'onMenuShareTimeline',
                'onMenuShareAppMessage',
                'onMenuShareQQ',
                'onMenuShareWeibo',
                'onMenuShareQZone',
                'checkJsApi',
                'openAddress'
            ]);
            $this->assign('signPackage', $signPackage);
        }
    }



}