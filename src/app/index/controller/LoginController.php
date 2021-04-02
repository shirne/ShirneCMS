<?php

namespace app\index\controller;

use app\common\model\MemberModel;
use app\common\model\MemberOauthModel;
use app\common\service\CheckcodeService;
use app\common\validate\MemberValidate;
use shirne\sdk\OAuthFactory;
use shirne\captcha\Captcha;
use think\facade\Db;
use think\Exception;
use think\facade\Log;

/**
 * 用户本地登陆和第三方登陆
 */
class LoginController extends BaseController{



    public function initialize()
    {
        parent::initialize();
        $this->assign('navmodel','member');
    }

    public function index($type=0)
    {
        if($this->userid){
            $this->success('您已登录',aurl('index/member/index'));
        }
        //方式1：本地账号登陆
        if(empty($type)){
            if($this->request->isPost()){
                $this->checkSubmitRate(2);
                $code = $this->request->post('verify','','strtolower');
                //验证验证码是否正确
                if(!($this->check_verify($code))){
                    $this->error(lang('Verify code error!'));
                }

                $data['username'] = $this->request->post('username');
                $password = $this->request->post('password');
                $member = Db::name('member')->where($data)->find();
                if(!empty($member) && $member['password']==encode_password($password,$member['salt'])){

                    if($member['status']==0){
                        user_log($member['id'], 'login', 0, '账号已禁用' );
                        $this->error(lang('Account is disabled!'));
                    }else {
                        $this->setLogin($member);
                        $remember = $this->request->post('remember');
                        if($remember){
                            $this->setAotuLogin($member);
                        }
                        $redirect=redirect()->restore();
                        if(empty($redirect->getData())){
                            $url=aurl('index/member/index');
                        }else{
                            $url=$redirect->getTargetUrl();
                        }

                        if(!empty($this->wechatUser)){
                            Db::name('memberOauth')->where('openid',$this->wechatUser['openid'])
                                ->update(['member_id'=>$member['id']]);
                        }

                        $this->success(lang('Login success!'),$url);
                    }
                }else{
                    user_log($member['id'],'login',0,'密码错误:'.$password);
                    $this->error(lang('Account or password incorrect!'));
                }
            }
            return $this->fetch();
        }else {
            $app = Db::name('OAuth')->where('id|type',$type)->find();
            if (empty($app)) {
                $this->error("不允许使用此方式登陆");
            }

            $callbackurl = url('index/login/callback', ['type' => $type], false,true);

            // 使用第三方登陆
            $oauth = OAuthFactory::getInstence($app['type'], $app['appid'], $app['appkey'], $callbackurl);
            $url=$oauth->redirect();

            return redirect($url->getTargetUrl());
        }
    }

    //登录回调地址
    public function callback($type = null, $code = null) 
    {
        if(empty($type) || empty($code)){
            $this->error('参数错误');
        }
        $callbackurl = url('index/login/callback', ['type' => $type],false,true);
        if(preg_match('/_\d+$/',$type)>0){
            list($type,$type_id)=explode('_',$type);
            if(!in_array($type,['wechat']))$this->error('参数错误');
            $app = Db::name($type)->where('id',$type_id)
                ->find();
            $oauth=OAuthFactory::getInstence($type, $app['appid'], $app['appsecret'],$callbackurl,true);
        }else{
            $type_id=$type;
            $type='oauth';
            $app = Db::name('OAuth')
                ->where('id|type',$type_id)
                ->find();
            $type_id=$app['id'];
            $oauth=OAuthFactory::getInstence($app['type'], $app['appid'], $app['appkey'],$callbackurl);
        }

        try {
            $userInfo = $oauth->user();
            $data['openid'] = $userInfo['id'];
            $data['nickname'] =$userInfo['nickname'];
            $data['name'] =$userInfo['name'];
            $data['email'] =$userInfo['email'];
            $data['avatar'] =$userInfo['avatar'];

            $origin=$userInfo->getOriginal();
            $data['gender'] = empty($origin['gender'])?0:$this->parseGender($origin['gender']);
            $data['unionid'] = empty($origin['unionid'])?'':$origin['unionid'];
            $data['data']=json_encode($origin);
            $data['type'] = $type;
            $data['type_id'] = $type_id;
            if($this->isLogin) {
                $data['member_id']=$this->userid;
            }elseif(!empty($userInfo['unionid'])){
                $sameAuth=MemberOauthModel::where('unionid',$userInfo['unionid'])->find();
                if(!empty($sameAuth)){
                    $data['member_id']=$sameAuth['member_id'];
                }
            }
            $model = MemberOauthModel::where('openid', $data['openid'])->find();
            if (empty($model)) {
                if(!isset($data['member_id']))$data['member_id']=0;
                $model = MemberOauthModel::create($data);
            } else {
                if($data['member_id']) {
                    if($model['member_id'] && $model['member_id']!=$data['member_id']){
                        //todo 自动生成的账户资料处理
                    }
                }
                $model->save($data);
            }
            if($this->isLogin){
                $this->success('绑定成功',redirect()->restore(aurl('index/member/index'))->getTargetUrl());
            }
            
            if (empty($model['member_id'])) {
                //根据设置自动生成账户
                if($this->config['m_register']!='1') {
                    $member = MemberModel::createFromOauth($model,session('agent'));
                    $model->save(['member_id' => $member['id']]);
                }
            }
            session('openid',$data['openid']);
            if($model['member_id']) {
                $member = Db::name('Member')->find($model['member_id']);
                //更新昵称和头像
                if(!empty($model['avatar']) &&
                    (empty($member['avatar']) || is_wechat_avatar($member['avatar']))
                ){
                    Db::name('member')->where('id',$member['id'])->update(
                        [
                            'nickname'=>$model['nickname'],
                            'avatar'=>$model['avatar']
                        ]
                    );
                }

                $this->setLogin($member);
            }
        }catch(Exception $e){
            Log::record($e->getMessage()."\n".$e->getFile().$e->getLine().$e->getCode(),'error');
            $this->error('登录失败',url('index/login/index'));
        }
        return redirect()->restore(aurl('index/member/index'));
    }

    private function parseGender($gender){
        if(in_array($gender,['0','1','2'])){
            return $gender;
        }
        if(strpos($gender,'男')!==false){
            return 1;
        }
        if(strpos($gender,'女')!==false){
            return 2;
        }
        return 0;
    }

    public function getpassword(){
        if($this->request->isPost()) {
            $step = $this->request->post('step/d', 1);
            $username = $this->request->post('username');
            $authtype = $this->request->post('authtype');

            if ($step == 2 || $step == 3) {

                if (empty($username)) $this->error("请填写用户名");
                if (empty($authtype)) $this->error("请选择认证方式");
                $user = Db::name('member')->where('username', $username)->find();
                if (empty($user)) {
                    $this->error("该用户不存在");
                }
                if (empty($user[$authtype . '_bind'])) $this->error("认证方式无效");

                $result=[];
                switch ($authtype) {
                    case 'email':
                        $result['sendtoname']="邮箱";
                        $result['sendto']=maskemail($user[$authtype]);
                        break;
                    case 'mobile':
                        $result['sendtoname']="手机";
                        $result['sendto']=maskphone($user[$authtype]);
                        break;
                }
                $service=new CheckcodeService();
                if($step==2){
                    $sendto = $user[$authtype];
                    $verify = $this->request->post('verify');
                    if(!$this->check_verify($verify)){
                        $this->error('请填写正确的图形验证码');
                    }
                    $result=$service->sendCode($authtype,$sendto);
                    if($result) {
                        $this->success('', '', $result);
                    }else{
                        $this->success('验证码发送失败', '');
                    }
                }
                if ($step == 3) {

                    $sendto = $user[$authtype];
                    $code = $this->request->post('checkcode');
                    if ($service->verifyCode($sendto,$code)) {
                        session('passed', $username);
                        $this->success('验证通过');
                    } else {
                        $this->error("验证码已失效");
                    }
                }
            }

            if ($step == 4) {
                $passed = session('passed');
                if (empty($passed)) {
                    $this->error("非法操作");
                }
                $password = $this->request->post('password');
                $repassword = $this->request->post('repassword');

                if (empty($password)) $this->error("请填写密码");
                if (strlen($password) < 6 || strlen($password) > 20) $this->error("密码长度 6-20");

                if ($password != $repassword) {
                    $this->error("两次密码输入不一致");
                }
                $data['salt'] = random_str(8);
                $data['password'] = encode_password($password, $data['salt']);
                $data['update_time'] = time();
                if (Db::name('member')->where('username', $passed)->update($data)) {
                    $this->success("密码设置成功", url('index/login/index'));
                }else{
                    $this->error('密码设置失败');
                }
            }
        }

        return $this->fetch();
    }
    public function checkusername(){
        Log::close();
        $username=$this->request->post('username');
        if(empty($username))$this->error("请填写用户名");
        $user=Db::name('member')->where('username',$username)->find();
        if(empty($user)){
            $this->error("该用户不存在");
        }
        $types=array();
        if($user['email_bind'])$types[]='email';
        if($user['mobile_bind'])$types[]='mobile';
        if(empty($types)) {
            $this->error("您的帐户未绑定任何有效资料，请联系客服处理。");
        }else{
            $this->success('', '',$types);
        }
    }

    public function register(){
        $this->seo("会员注册");


        if($this->request->isPost()){
            $this->checkSubmitRate(2);
            $data=$this->request->only('username,password,repassword,email,realname,mobile,mobilecheck','post');

            $validate=new MemberValidate();
            $validate->setId();
            if(!$validate->scene('register')->check($data)){
                $this->error($validate->getError());
            }

            $invite_code=$this->request->post('invite_code');
            if(($this->config['m_invite']==1 && !empty($invite_code)) || $this->config['m_invite']==2) {
                if (empty($invite_code)) $this->error("请填写激活码");
                $invite = Db::name('inviteCode')->where(array('code' => $invite_code, 'is_lock' => 0, 'member_use' => 0))->find();
                if (empty($invite) || ($invite['invalid_time'] > 0 && $invite['invalid_time'] < time())) {
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
                $data['referer']=session('agent');
                $data['level_id']=getDefaultLevel();
            }
            $data['salt']=random_str(8);
            $data['password']=encode_password($data['password'],$data['salt']);
            $data['login_ip']=$this->request->ip();

            unset($data['repassword']);
            $model=MemberModel::create($data);

            if(empty($model['id'])){
                Db::rollback();
                $this->error("注册失败");
            }
            if(!empty($invite)) {
                $invite['member_use'] = $model['id'];
                $invite['use_time'] = time();
                Db::name('invite_code')->update($invite);
            }
            if(!empty($this->wechatUser)){
                Db::name('memberOauth')->where('openid',$this->wechatUser['openid'])
                    ->update(['member_id'=>$model['id']]);
            }
            Db::commit();
            $this->setLogin($model);
            $redirect=redirect()->restore();
            if(empty($redirect->getData())){
                $url=aurl('index/member/index');
            }else{
                $url=$redirect->getTargetUrl();
            }
            $this->success("注册成功",$url);
        }

        $this->assign('nocode',$this->config['m_invite']<1);
        return $this->fetch();
    }

    public function checkunique($type='username'){
        Log::close();
        if(!in_array($type,array('username','email','mobile'))){
            $this->error('参数不合法');
        }
        $member=Db::name('member');
        $val=$this->request->get('value');
        $m=$member->where($type,$val)->find();
        $json=array();
        $json['error']=0;
        if(!empty($m))$json['error']=1;
        return json($json);
    }

    public function send_checkcode($mobile,$code){

        //图形验证码
        if(!$this->check_verify($code)){
            $this->error('验证码错误');
        }

        //号码格式验证
        if(!preg_match('/^1[2-9]\d{9}$/',$mobile)){
            $this->error('手机号码格式错误');
        }

        //已注册验证
        $member=Db::name('member')->where('mobile',$mobile)->find();
        if(!empty($member)){
            $this->error('该手机号码已注册');
        }

        $service=new CheckcodeService();
        $sended=$service->sendCode('mobile',$mobile);
        if($sended) {
            $this->success('验证码发送成功！');
        }else{
            $this->error($service->getError());
        }
    }

    public function verify(){
        $verify = new Captcha(array('seKey'=>config('session.sec_key')));

        $verify->fontSize = 13;
        $verify->length = 4;
        return $verify->entry('foreign');
    }
    protected function check_verify($code){
        $verify = new Captcha(array('seKey'=>config('session.sec_key')));
        return $verify->check($code,'foreign');
    }

    public function logout()
    {
        $this->clearLogin();
        
        $this->success("已成功退出登陆");

    }

    /**
     * 忘记密码
     */
    public function forgot()
    {
        return $this->fetch();
    }
}
