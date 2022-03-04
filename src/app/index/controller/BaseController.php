<?php
namespace app\index\controller;

use app\common\model\MemberLevelModel;
use app\common\model\MemberModel;
use app\common\model\WechatModel;
use app\common\service\EncryptService;
use EasyWeChat\Factory;
use extcore\traits\Email;
use shirne\sdk\OAuthFactory;
use app\BaseController as Controller;
use think\facade\Db;
use think\Exception;
use think\facade\Env;
use think\facade\Lang;
use think\facade\Log;

/**
 * 前端的控制器基类
 * Class BaseController
 * @package app\index\controller
 */
class BaseController extends Controller
{
    use Email;

    protected $userid;
    protected $user;
    protected $openid;
    protected $wechat;
    protected $wechatUser;
    protected $userLevel;
    protected $isLogin=false;
    protected $errMsg;
    protected $config=array();
    protected $isWechat=false;
    protected $isMobile=false;
    protected $seoIsInit=false;

    protected $lang;
    protected $lang_switch;
    
    /**
     * 前端初始化
     * @throws Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function initialize(){
        parent::initialize();

        //初始化语言
        $this->lang_switch=config('lang.switch_on',false);
        if($this->lang_switch){
            $cookie_var=config('lang.cookie_var');

            $this->lang=Lang::range();

            if($this->lang != cookie($cookie_var)){
                cookie($cookie_var,$this->lang);
            }
        }

        $this->config=getSettings();
        $this->assign('config',$this->config);

        $this->checkPlatform();

        if(isset($this->config['site-close']) && $this->config['site-close'] == 1){
            if($this->request->get('force') == 1){
                session('noclose-force',1);
            }
            if(session('noclose-force')!=1){
                $this->error($this->config['site-close-desc']);
            }
        }

        // POST请求自动检查操作频率
        if((config('app.auto_check_submit_rate') || (isset($this->config['auto_check_submit_rate']) && $this->config['auto_check_submit_rate']))
            && $this->request->isPost()){
            $this->checkSubmitRate($this->config['submit_rate']?:2);
        }

        $navigation=config('navigator');
        $navigation=parseNavigator($navigation,app('http')->getName());
        $this->assign('navigator',$navigation);
        $this->assign('navmodel','index');

        $agent = $this->request->param('agent');
        if(!empty($agent)){
            $agent=preg_replace('/[^a-zA-Z0-9_-]*/','',$agent);
            if($agent) {
                Log::info('With Agent code: ' . $agent );
                session('agent', $agent);
            }
        }

        $this->checkLogin();

        if($this->isLogin && empty($this->user['referer'])){
            $agent = session('agent');
            if($agent){
                MemberModel::autoBindAgent($this->user,$agent);
                session('agent',null);
            }
        }

        $this->assign('isLogin',$this->isLogin);
        $this->assign('protocol',$this->request->scheme());

        $this->seo();
    }
    
    /**
     * @param int $time
     */
    protected function checkSubmitRate($time = 2){
        
        if (!$time) $time = 2;
        $key = '__check_submit_rate__';
        $lasttime = session($key);
        if ($lasttime) {
            if (time() - $lasttime <= $time) {
                $this->error(lang('Too frequent operation, Please try again later!'));
            }
        }
        session($key, time());
    }
    
    public function _empty(){
        
        return $this->errorPage();
    }

    protected function errorPage($error='',$description='', $redirect=null){
        if(empty($error)){
            $error = lang('Page not found!');
        }
        $this->assign('error',$error);
        $this->assign('description',$description);
        if(empty($redirect))$redirect=url('index/index/index');
        $this->assign('redirect',$redirect);
        return $this->fetch('public/empty')->code(404);
    }
    
    /**
     * 设置seo信息
     * @param string $title
     * @param string $keywords
     * @param string $desc
     */
    protected function seo($title='',$keywords='',$desc=''){
        $sitename=$this->config['site-webname'];
        if(empty($title)){
            $title = $sitename;
        }elseif($title!=$sitename){
            $title .= ' - '.$sitename;
        }
        
        $this->assign('title',$title);

        if(empty($keywords) && !$this->seoIsInit){
            $keywords = $this->config['site-keywords'];
        }
        if(!$this->seoIsInit || !empty($keywords)){
            $this->assign('keywords',$keywords);
        }

        if(empty($desc) && !$this->seoIsInit){
            $desc = $this->config['site-description'];
        }
        if(!$this->seoIsInit || !empty($desc)){
            $this->assign('description',$desc);
        }

        $this->seoIsInit = true;
    }

    /**
     * 写入会员登录状态
     * @param $member
     * @throws Exception
     */
    protected function setLogin($member, $logintype = 1){
        if($member['status']!='1'){
            $this->error('会员已禁用');
        }
        session('userid', $member['id']);
        session('username',empty($member['realname'])? $member['username']:$member['realname']);
        $time=time();
        session('logintime',$time);
        if($logintype == 1){
            Db::name('member')->where('id',$member['id'])->update(array(
                'login_ip'=>request()->ip(),
                'logintime'=>$time
            ));
            user_log($member['id'], 'login', 1, '登录成功');
        }
    }

    /**
     * 清除会员登录状态
     * @param bool $log
     */
    protected function clearLogin($log=true){
        $id=session('userid');
        if($log && !empty($id)) {
            user_log($id, 'logout', 1, '退出登录');
        }

        session('userid',null);
        session('username',null);
        session('logintime',null);
        
        cookie('login', null);
    }

    protected function setAotuLogin($member, $days = 7){

        $expire = $days * 24 * 60 * 60;
        $timestamp = time() + $expire;
        $data = EncryptService::getInstance()->encrypt(json_encode(['id'=>$member['id'],'time'=>$timestamp]));
        cookie('login', $data, $expire);
    }

    /**
     * 检测用户是否登录并初始化资料
     * @throws Exception
     */
    protected function checkLogin(){
        $this->userid = session('userid');

        $loginsession = $this->request->cookie('login');
        if(!empty($loginsession)){
            cookie('login',null);
            $data = EncryptService::getInstance()->decrypt($loginsession);
            if(!empty($data)){
                $json = json_decode($data, true);
                if(!empty($json['id'])){
                    $timestamp = $json['time'];
                    if($timestamp >= time()){
                        $this->userid = $json['id'];
                        $member = MemberModel::where('id',$this->userid)->find();
                        $this->setLogin($member, 0);
                        $this->user = $member;
                        $this->setAotuLogin($member);
                    }
                }
            }
        }

        if(!empty($this->userid)){
            if(empty($this->user)){
                $this->user = MemberModel::where('id',$this->userid)->find();
            }
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
                $this->clearLogin(false);
                $this->error("登录失效",url('index/login/index'));
            }
        }

        if($this->wechatLogin() && $this->config['wechat_autologin']=='1' ){
            redirect()->remember();

            $callbackurl = url('index/login/callback', ['type' => 'wechat_'.$this->wechat['id']], true,true);

            // 使用第三方登陆
            $oauth = OAuthFactory::getInstence('wechat', $this->wechat['appid'], $this->wechat['appsecret'], $callbackurl,true);
            $oauth->redirect()->send();
            exit;
        }
        $this->assign('wechatUser', $this->wechatUser);
    }

    /**
     * 检测并自动登录微信
     * @return bool
     * @throws Exception
     */
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

        $this->openid=$openid=session('openid');

        //跳过登录页面
        if(strtolower($this->request->controller())=='login' &&
            (strtolower($this->request->action())=='index' || strtolower($this->request->action())=='callback')
        ){
            if(!empty($openid)){
                $wechatUser=Db::name('memberOauth')->where('openid',$openid)->find();
                if($wechatUser['member_id']){
                    $member=MemberModel::find($wechatUser['member_id']);
                    if(!empty($member)) {
                        $this->setLogin($member);

                        get_redirect(aurl('index/member/index'))->send();
                        exit;
                    }
                }
                $this->wechatUser=$wechatUser;
                return false;
            }
            return false;
        }

        if($this->isLogin){
            if(empty($openid)) {
                $wechatUser = Db::name('memberOauth')
                    ->where('member_id', $this->userid)
                    ->where('type_id', $this->wechat['id'])
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
                    $member=MemberModel::find($wechatUser['member_id']);
                    if(!empty($member)) {
                        $this->setLogin($member);

                        get_redirect(aurl('index/member/index'))->send();
                        exit;
                    }
                }
                $this->wechatUser=$wechatUser;
                return false;
            }
        }
        return true;
    }

    /**
     * 初始化会员等级资料
     */
    protected function initLevel(){
        if($this->isLogin && empty($this->userLevel)){
            $this->userLevel=getMemberLevel($this->user['level_id']);
        }
    }

    /**
     * 检测客户端平台，并注册对应平台的环境所需资源
     */
    protected function checkPlatform(){
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

        $base_path=config('template.view_path');

        //加载模板语言
        $temp_lang=$base_path.'lang'.DIRECTORY_SEPARATOR.$this->lang.'.php';
        if(is_file($temp_lang)){
            Lang::load($temp_lang);
        }

        if(config('template.independence')){
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
        if($this->isWechat ) {
            $signPackage = $this->getShareData();

            if(!empty($signPackage)) {
                $this->assign('signPackage', $signPackage);
            }
        }
    }

    private $currentWechats=[];
    protected function getWechatAccount($type,$force=false){
        if(!isset($this->currentWechats[$type]) || $force) {
            $this->currentWechats[$type] = cache('default_' . $type);
            if (empty($wechat) || $force == true) {
                $wechat = \think\facade\Db::name('Wechat')->where('type', $type)
                    ->where('account_type', 'service')
                    ->order('is_default DESC')->find();
                cache('default_' . $type, $wechat,['expire'=>60*60*12]);
                $this->currentWechats[$type] = $wechat;
            }
        }
        return $this->currentWechats[$type];
    }

    protected function getShareData($url = '')
    {
        $this->wechat=$this->getWechatAccount('wechat');
        if(!empty($this->wechat['appid'])) {
            $app = Factory::officialAccount(WechatModel::to_config($this->wechat));
            if($url)$app->jssdk->setUrl($url);
            $signPackage = $app->jssdk->buildConfig([
                'updateAppMessageShareData',
                'updateTimelineShareData',
                'onMenuShareTimeline',
                'onMenuShareAppMessage',
                'onMenuShareQQ',
                'onMenuShareWeibo',
                'onMenuShareQZone',
                'checkJsApi',
                'openAddress',
                'openLocation',
                'getLocation'
            ]);
            return $signPackage;
        }
        return [];
    }


}