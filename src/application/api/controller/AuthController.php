<?php

namespace app\api\controller;

use app\api\facade\MemberTokenFacade;
use app\common\model\MemberModel;
use app\common\model\MemberOauthModel;
use app\common\model\OauthAppModel;
use app\common\model\WechatModel;
use app\common\validate\MemberValidate;
use EasyWeChat\Factory;
use EasyWeChat\OfficialAccount\Application;
use shirne\captcha\Captcha;
use think\Db;
use think\facade\Cache;
use think\facade\Env;
use think\facade\Log;

/**
 * 授权相关操作
 * Class AuthController
 * @package app\api\Controller
 */
class AuthController extends BaseController
{
    protected $accessToken='';
    protected $accessSession=[];

    public function initialize(){
        parent::initialize();

        $this->accessToken = request()->header('access_token');
        if(!$this->accessToken){
            $this->accessToken = request()->param('access_token');
        }
        if($this->accessToken){
            $session = cache('access_'.$this->accessToken);
            if(!empty($session)){
                $sessData = json_decode($session, true);
                if(!empty($sessData)){
                    $this->accessSession = $sessData;
                }else{
                    $this->accessToken = '';
                }
            }
        }
        if(empty($this->accessToken) &&
            !in_array($this->request->action(), ['token','wxlogin','refresh'])
            ){
            $this->error('未授权访问',ERROR_LOGIN_FAILED);
        }
    }

    public function __destruct()
    {
        if($this->accessToken){
            if(empty($this->accessSession)){
                $this->accessSession=['time'=>time()];
            }
            cache(
                'access_'.$this->accessToken, 
                json_encode($this->accessSession, JSON_UNESCAPED_UNICODE),
                ['expire'=>60*10]
            );
        }
    }

    private function getIpKey(){
        $ip = $this->request->ip();
        return 'access_'.str_replace([':','.'],'_',$ip);
    }

    public function token($appid, $agent = ''){
        $app=$this->getApp($appid);
        if(empty($app)){
            $this->error('未授权APP',ERROR_LOGIN_FAILED);
        }

        if($this->accessToken){
            cache('access_'.$this->accessToken, null);
            $this->accessToken='';
            $this->accessSession=[];
        }

        // 根据IP限制token获取频率
        $ipkey = $this->getIpKey();
        $ipcount = cache($ipkey);
        if(!$ipcount){
            cache($ipkey, 1, ['expire'=>60*60]);
        }else{
            if($ipcount >= 10){
                $this->error('操作过于频繁');
            }
            cache($ipkey, $ipcount+1, ['expire'=>60*60]);
        }

        $this->accessToken = $this->createToken();
        $this->accessSession['appid']=$appid;

        if($agent){
            $agentMember = Db('member')->where('agentcode',$agent)
                ->where('status',1)
                ->where('is_agent','gt',0)->find();
            if(!empty($agentMember)){
                $this->accessSession['agent'] = $agentMember['id'];
            }
        }

        return $this->response($this->accessToken);
    }
    private function createToken(){
        $token = md5(config('app.app_key').time().microtime().mt_rand(999,9999));
        
        while(Cache::has('access_'.$token)){
            $token = md5(config('app.app_key').time().microtime().mt_rand(999,9999));
        }
        return $token;
    }

    private function getApp($appid){
        if(empty($appid)){
            return false;
        }
        $app=OauthAppModel::where('appid',$appid)->find();
        if(empty($app)){
            return false;
        }
        return $app;
    }

    public function login($username, $password){
        
        $this->check_submit_rate(2,'global',md5($username));
        $data = $this->request->put();
        $app=$this->getApp($this->accessSession['appid']);
        if(empty($app)){
            $this->error('未授权APP',ERROR_LOGIN_FAILED);
        }
        
        if(!empty($this->accessSession['need_verify'])){
            if(empty($data['verify'])){
                $this->error('请填写验证码',ERROR_NEED_VERIFY);
            }
            $verify = new Captcha(array('seKey'=>config('session.sec_key')), Cache::instance());
            $checked = $verify->check($data['verify'],'_api_'.$this->accessToken);
            if(!$checked){
                $this->error('验证码错误',ERROR_NEED_VERIFY);
            }
        }
        
        if(empty($username) || empty($password)){
            $this->error('请填写登录账号及密码',ERROR_LOGIN_FAILED);
        }
        $errcount = $this->accessSession['error_count'];
        if($errcount > 4){
            $this->error('登录尝试次数过多',ERROR_LOGIN_FAILED);
        }
        $member = MemberModel::where('username',$username)->find();
        $respdata=[];
        if(!empty($member) ){
            $merrorcount = intval(cache('login_error_'.$member['id']));
            if($merrorcount > 4){
                $this->error('登录尝试次数过多',ERROR_LOGIN_FAILED);
            }
            if($member['status']==1) {
                if (compare_password($member, $password)) {
                    $token = MemberTokenFacade::createToken($member['id'], $app['platform'], $app['appid']);
                    if (!empty($token)) {
                        cache($this->getIpKey(),NULL);
                        user_log($member['id'], 'login', 1, '登录成功');
                        $this->accessSession['need_verify'] = 0;
                        $this->accessSession['error_count'] = 0;
                        cache('login_error_'.$member['id'], NULL);

                        $openid = $this->request->param('openid');
                        if(!empty($openid)){
                            $oauth = MemberOauthModel::where('openid',$openid)->find();
                            if(!empty($oauth)){
                                MemberOauthModel::where('openid',$openid)->where('member_id',0)->update(['member_id'=>$member['id']]);
                                $updata = MemberModel::checkUpdata($oauth,$member);
                                if(!empty($updata)){
                                    $member->save($updata);
                                }
                            }
                        }
                        return $this->response($token);
                    }
                } else {
                    user_log($member['id'], 'login', 0, '登录失败');
                    $this->accessSession['need_verify'] = 1;
                    $this->accessSession['error_count'] = $errcount + 1;
                    $respdata['need_verify']=1;
                    $merrorcount += 1;
                    cache('login_error_'.$member['id'],$merrorcount,['expire'=>60*60]);
                }
            }else{
                $this->error('账户已被禁用',ERROR_MEMBER_DISABLED);
            }
        }

        $this->error('登录失败',ERROR_LOGIN_FAILED,$respdata);
    }

    /**
     * 微信小程序登录
     * @return \think\response\Json
     */
    public function wxLogin($wxid, $code){
        
        $agent=$this->request->param('agent');
        $wechat=Db::name('wechat')->where('type','wechat')
            ->where('id|hash',$wxid)->find();
        if(empty($wechat)){
            $this->error('服务器配置错误',ERROR_LOGIN_FAILED);
        }
        $options=WechatModel::to_config($wechat);
        switch ($wechat['account_type']) {
            case 'wechat':
            case 'subscribe':
            case 'service':
                $weapp = Factory::officialAccount($options);
                break;
            case 'miniprogram':
            case 'minigame':
                $weapp=Factory::miniProgram($options);
                break;
            default:
                $this->error('配置错误',ERROR_LOGIN_FAILED);
                break;
        }

        if($weapp instanceof Application){
            $userinfo = $weapp->oauth->user()->getOriginal();
            if(empty($userinfo)){
                $this->error('登录失败', ERROR_LOGIN_FAILED);
            }
            $rowData = json_encode($userinfo, JSON_UNESCAPED_UNICODE);
            $session=['openid'=>$userinfo['openid'],'unionid'=>$userinfo['unionid']];
        }else{
            //调试模式允许mock登录
            if($wechat['is_debug'] && $code=='the code is a mock one'){
                $rowData = $this->request->param('rawData');
                $userinfo = json_decode($rowData, TRUE);
                $session=['openid'=>md5($userinfo['nickName']),'unionid'=>''];
            }else {
                $session = $weapp->auth->session($code);
                if (empty($session) || empty($session['openid'])) {
                    $this->error('登录失败', ERROR_LOGIN_FAILED);
                }

                $rowData = $this->request->param('rawData');
                if (!empty($rowData)) {
                    $signature = $this->request->param('signature');
                    if (sha1($rowData . $session['session_key']) == $signature) {
                        $userinfo = json_decode($rowData, TRUE);
                    }
                }
            }
        }
        if(empty($userinfo)){
            $this->error('登录授权失败',ERROR_LOGIN_FAILED);
        }
        $type=$wechat['account_type'];
        $typeid=$wechat['id'];

        $condition=array('openid'=>$session['openid']);
        $oauth=MemberOauthModel::where($condition)->find();
        if(!empty($oauth) && $oauth['member_id']) {
            $member = MemberModel::where('id', $oauth['member_id'])->find();
        }elseif($this->isLogin){
            $member=MemberModel::where('id', $this->user['id'])->find();
        }elseif($session['unionid']){
            $sameAuth=MemberOauthModel::where('unionid',$session['unionid'])->find();
            if(!empty($sameAuth)){
                $member=MemberModel::where('id',$sameAuth['member_id'])->find();
            }
        }
    
        $data=$this->wxMapdata($userinfo,$rowData);
        $data['type']=$type;
        $data['type_id']=$typeid;
        if(!empty($session['unionid']))$data['unionid']=$session['unionid'];
        
        if(empty($member)){
            $register=getSetting('m_register');
            if($register!='1'){
            
                //自动注册
                $data['openid']=$session['openid'];
                
                $referid = $this->getAgentId($agent);
                $member = MemberModel::createFromOauth($data, $referid);
                
                if($member['id']){
                    $data['member_id']=$member['id'];
                }
            }
            
        }else{
            //更新资料
            if(empty($oauth['member_id'])){
                $data['member_id'] = $member['id'];
            }
            $updata=MemberModel::checkUpdata($data, $member);
            if(!empty($updata)){
                MemberModel::update($updata,array('id'=>$member['id']));
            }
            if(empty($member['referer']) && !empty($agent) && $member['agentcode']!=$agent){
                $member->setReferer($agent);
            }
        }
        
        if(empty($oauth)){
            $data['openid']=$session['openid'];
            MemberOauthModel::create($data);
        }else{
            MemberOauthModel::update($data,['id'=>$oauth['id']]);
        }
        
        if($this->isLogin){
            return $this->response(['openid'=>$session['openid']]);
        }

        if(!empty($member)){

            if($member['status'] != 1){
                $this->error('账户已被禁用',ERROR_MEMBER_DISABLED, ['openid'=>$session['openid']]);
            }

            $token=MemberTokenFacade::createToken($member['id'],$wechat['type'].'-'.$wechat['account_type'], $wechat['appid']);
            if(!empty($token)) {
                MemberModel::update([
                    'login_ip'=>request()->ip(),
                    'logintime'=>time()
                ],['id'=>$member['id']]);
                user_log($member['id'],'login',1,'登录'.$wechat['title']);
                $token['openid']=$session['openid'];
                return $this->response($token);
            }
            
        }
        $this->error('登录失败',ERROR_LOGIN_FAILED);
    }
    
    private function getAgentId($agent){
        $referid=0;
        if(!empty($agent)){
            $amem=Db::name('Member')->where('is_agent','GT',0)
                ->where('agentcode',$agent)
                ->where('status',1)->find();
            if(!empty($amem)){
                Log::record('With Agent code: '.$agent.','.$amem['id']);
                $referid = $amem['id'];
            }
        }
        return $referid;
    }

    /**
     * 第三方登录数据转换
     * @param $userinfo
     * @param $rowData
     * @return array
     */
    private function wxMapdata($userinfo,$rowData){
        $nickname = '';
        if(isset($userinfo['nickName'])){
            $nickname = $userinfo['nickName'];
        }
        if(isset($userinfo['nickname'])){
            $nickname = $userinfo['nickname'];
        }

        $avatar = '';
        if(isset($userinfo['avatar'])){
            $avatar = $userinfo['avatar'];
        }
        if(isset($userinfo['avatarUrl'])){
            $avatar = $userinfo['avatarUrl'];
        }
        if(isset($userinfo['headimgurl'])){
            $avatar = $userinfo['headimgurl'];
        }
        $gender = '';
        if(isset($userinfo['gender'])){
            $gender = $userinfo['gender'];
        }
        if(isset($userinfo['sex'])){
            $gender = $userinfo['sex'];
        }
        return array(
            'data'=>$rowData,
            'is_follow'=>0,
            'nickname'=>$nickname,
            'gender'=>$gender,
            //'unionid'=>isset($userinfo['unionid'])?$userinfo['unionid']:'',
            'avatar'=>$avatar,
            'city'=>$userinfo['city'],
            'province'=>$userinfo['province'],
            'country'=>isset($userinfo['country'])?$userinfo['country']:'',
            'language'=>isset($userinfo['language'])?$userinfo['language']:''
        );
    }

    public function refresh($refresh_token){
        
        if(!empty($refresh_token)){
            $token=MemberTokenFacade::refreshToken($refresh_token);
            if(!empty($token)) {
                return $this->response($token);
            }
        }
        $this->error('刷新失败',ERROR_REFRESH_TOKEN_INVAILD);
    }

    public function captcha(){

        $verify = new Captcha(array('seKey'=>config('session.sec_key')), Cache::instance());

        $verify->fontSize = 16;
        $verify->length = 4;
        return $verify->entry('_api_'.$this->accessToken);
    }

    public function quit(){
        if($this->isLogin){
            MemberTokenFacade::clearToken($this->token);
        }
        $this->success('退出成功');
    }

    /**
     * todo 验证码
     */
    public function verify(){

    }

    /**
     * todo 忘记密码
     */
    public function forget($step = 0){
        $app=$this->getApp($this->accessSession['appid']);
        if(empty($app)){
            $this->error('未授权APP',ERROR_LOGIN_FAILED);
        }

        //第一步:确认账号
        if($step == 0){
            $account = $this->request->param('account');
            $account_type = $this->request->param('type');
            $model = Db::name('member')->where('status',1);
            if($account_type == 'mobile'){
                $model->where('mobile',$account)->where('mobile_bind',1);
            }elseif($account_type=='email'){
                $model->where('email',$account)->where('email_bind',1);
            }else{
                $this->error('账号类型错误');
            }
            $member = $model->find();
            if(empty($member)){
                $this->error('账号错误');
            }
            if($account_type == 'mobile'){
                //发送验证码
                $verify = '';
            }elseif($account_type=='email'){
                //发送验证码
                $verify = '';
            }
            $this->accessSession['forget_account']=$member['id'];
            $this->accessSession['forget_verify']=$verify;
            $this->success('验证码已发送');

        //第二步:验证验证码
        }elseif($step == 1){
            if(empty($this->accessSession['forget_account'])){
                $this->success('验证失效,请重新填写账号');
            }
            $verifycode = $this->request->param('verify');
            if(empty($this->accessSession['forget_verify'])){
                $this->error('验证码已失效');
            }
            if($verifycode != $this->accessSession['forget_verify']){
                $this->error('验证码错误');
            }

            $this->accessSession['forget_pass']=1;
            $this->success('验证通过');

        //第三步:重置密码
        }elseif($step == 2){
            if(empty($this->accessSession['forget_account'])){
                $this->success('验证失效,请重新填写账号');
            }
            if(empty($this->accessSession['forget_pass'])){
                $this->success('验证失效,请重新发送验证码');
            }
            $password = $this->request->param('password');
            $repassword = $this->request->param('repassword');

            if($password != $repassword){
                $this->success('两次密码输入不一致，请确认输入');
            }

            $data['salt']=random_str(8);
            $data['password']=encode_password($password,$data['salt']);
            Db::name('member')->where('id',$this->accessSession['forget_account'])->update($data);
            $this->success('密码重置成功!');
        }
    }

    /**
     * 注册会员
     */
    public function register($agent = ''){
        $this->check_submit_rate(2);
        $app=$this->getApp($this->accessSession['appid']);
        if(empty($app)){
            $this->error('未授权APP',ERROR_LOGIN_FAILED);
        }

        // 未开启手机验证码的情况下验证图形码
        if($this->config['sms_code'] != 1) {
            $verifycode = $this->request->param('verify');
            if(empty($verifycode)){
                $this->error('请填写验证码',ERROR_NEED_VERIFY);
            }
            $verify = new Captcha(array('seKey'=>config('session.sec_key')), Cache::instance());
            $checked = $verify->check($verifycode,'_api_'.$this->accessToken);
            if(!$checked){
                $this->error('验证码错误',ERROR_NEED_VERIFY);
            }
        }

        $data=$this->request->only('username,password,repassword,email,realname,mobile,mobilecheck','post');

        $validate=new MemberValidate();
        $validate->setId();
        if(!$validate->scene('register')->check($data)){
            $this->error($validate->getError());
        }

        $invite_code=$this->request->post('invite_code');
        if(($this->config['m_invite']==1 && !empty($invite_code)) || $this->config['m_invite']==2) {
            if (empty($invite_code)) $this->error("请填写激活码");
            $invite = Db::name('invite_code')->where(array('code' => $invite_code, 'is_lock' => 0, 'member_use' => 0))->find();
            if (empty($invite) || ($invite['invalid_at'] > 0 && $invite['invalid_at'] < time())) {
                $this->error("激活码不正确");
            }
        }

        if($this->config['sms_code'] == 1) {
            if (empty($data['mobilecheck'])) {
                $this->error(' 请填写手机验证码');
            }
            $service=new CheckcodeService();
            $verifyed=$service->verifyCode($data['mobile'],$data['mobilecheck']);
            if(!$verifyed){
                $this->error(' 手机验证码填写错误');
            }
            $data['mobile_bind']=1;
            unset($data['mobilecheck']);
        }

        $openid = $this->request->param('openid');
        if(empty($openid)){
            $openid = $this->accessSession['openid'];
        }

        Db::startTrans();
        if(!empty($invite)) {
            $invite = Db::name('invite_code')->lock(true)->find($invite['id']);
            if (!empty($invite['member_use'])) {
                Db::rollback();
                $this->error("激活码已被使用");
            }
            $data['referer']=$invite['member_id'];
            if($invite['level_id']){
                $data['level_id']=$invite['level_id'];
            }else{
                $data['level_id']=getDefaultLevel();
            }
        }else{
            $agentid = isset($this->accessSession['agent'])?intval($this->accessSession['agent']):0;
            if($agent){
                $agentMember = Db('member')->where('agentcode',$agent)
                    ->where('status',1)
                    ->where('is_agent','gt',0)->find();
                if(!empty($agentMember)){
                    $agentid = $agentMember['id'];
                }
            }
            $data['referer']=$agentid;
            $data['level_id']=getDefaultLevel();
        }
        $data['salt']=random_str(8);
        $data['password']=encode_password($data['password'],$data['salt']);
        $data['login_ip']=$this->request->ip();

        unset($data['repassword']);
        if(!empty($openid)){
            $oauth = MemberOauthModel::where('openid',$openid)->find();
            if(!empty($oauth)){
                $updata = MemberModel::checkUpdata($oauth,$data);
                $data = array_merge($data, $updata);
            }else{
                $openid = '';
            }
        }
        $model=MemberModel::create($data);

        if(empty($model['id'])){
            Db::rollback();
            $this->error("注册失败");
        }
        if(!empty($invite)) {
            $invite['member_use'] = $model['id'];
            $invite['use_at'] = time();
            Db::name('invite_code')->update($invite);
        }
        if(!empty($this->accessSession['openid'])){
            Db::name('memberOauth')->where('openid',$this->accessSession['openid'])
                ->update(['member_id'=>$model['id']]);
        }
        if(!empty($openid)){
            MemberOauthModel::where('openid',$openid)->where('member_id',0)->update(['member_id'=>$model['id']]);
        }
        Db::commit();
        $token = MemberTokenFacade::createToken($model['id'], $app['platform'], $app['appid']);

        $this->success("注册成功",1,$token);
    }

}