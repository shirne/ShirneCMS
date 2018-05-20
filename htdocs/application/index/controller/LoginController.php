<?php

namespace app\index\controller;
use app\common\model\MemberModel;
use app\common\model\MemberOauthModel;
use app\common\validate\MemberValidate;
use sdk\OAuthFactory;
use sdk\WechatAuth;
use think\Db;
use think\Exception;

/**
 * 用户本地登陆和第三方登陆
 */
class LoginController extends BaseController{



    public function initialize(){
        parent::initialize();
    }

    public function index($type=0)
    {
        if($this->userid){
            $this->success('您已登录',url('index/member/index'));
        }
        //方式1：本地账号登陆
        if(empty($type)){
            if($this->request->isPost()){
                $code = $this->request->post('verify','','strtolower');
                //验证验证码是否正确
                if(!($this->check_verify($code))){
                    $this->error('验证码错误');
                }

                $data['username'] = $this->request->post('username');
                $password = $this->request->post('password');
                $member = Db::name('member')->where($data)->find();
                if(!empty($member) && $member['password']==encode_password($password,$member['salt'])){

                    if($member['status']==0){
                        user_log($member['id'], 'login', 0, '账号已禁用' );
                        $this->error("您的账号已禁用");
                    }else {
                        setLogin($member);
                        $redirect=redirect()->restore();
                        if(empty($redirect->getData())){
                            $url=url('index/member/index');
                        }else{
                            $url=$redirect->getTargetUrl();
                        }

                        $this->success("登陆成功",$url);
                    }
                }else{
                    user_log($member['id'],'login',0,'密码错误:'.$password);
                    $this->error("账号或密码错误");
                }
            }
            return $this->fetch();
        }else {
            $app = Db::name('OAuth')->find(['id|type'=>$type]);
            if (empty($app)) {
                $this->error("不允许使用此方式登陆");
            }
            $type=$app['id'];
            $callbackurl = url('index/login/callback', ['type' => $type]);

            // 使用第三方登陆
            $oauth = OAuthFactory::getInstence($app['type'], $app['appid'], $app['appkey'], $callbackurl);
            $url=$oauth->getAuthUrl();
            session('OAUTH_'.$type.'_STATE',$oauth->state);
            return redirect($url);
        }
    }

    //登录回调地址
    public function callback($type = null, $code = null) 
    {
      
        if(empty($type) || empty($code)){
            $this->error('参数错误');  
        }
        $app = Db::name('OAuth')->find(['id'=>$type]);
        $oauth=OAuthFactory::getInstence($app['type'], $app['appid'], $app['appkey']);
        $oauth->getAccessToken(session('OAUTH_'.$type.'_STATE'));
        try {
            $userInfo = $oauth->getUserInfo();
            $data = call_user_func([$this, 'map_' . $app['type'] . '_info'], $userInfo);
            $data['type'] = $app['type'];
            $data['type_id'] = $type;
            $model = MemberOauthModel::get(['openid' => $data['openid']]);
            if (empty($model)) {
                if (empty($data['member_id'])) {
                    $member = MemberModel::create([
                        'username' => $data['openid'],
                        'realname' => $data['nickname'],
                        'avatar' => $data['avatar']
                    ]);
                    $data['member_id'] = $member['id'];
                }
                MemberOauthModel::create($data);
            } else {
                unset($data['member_id']);
                $model->save($data);
            }
            $member = Db::name('Member')->find($model['member_id']);
        }catch(Exception $e){
            $this->error('登录失败');
        }

        setLogin($member);
        $this->success('登录成功');
        
    }

    private function map_oschina_info($userInfo){
        $data=array();
        $data['openid'] = $userInfo['openid'];
        $data['nickname'] =$userInfo['nickname'];
        $data['avatar'] =$userInfo['avatar'];
        $data['data']=json_encode($userInfo);
        return $data;
    }
    private function map_github_info($userInfo){
        $data=array();
        $data['openid'] = $userInfo['openid'];
        $data['nickname'] =$userInfo['nickname'];
        $data['avatar'] =$userInfo['avatar'];
        $data['data']=json_encode($userInfo);
        return $data;
    }
    private function map_gitee_info($userInfo){
        $data=array();
        $data['openid'] = $userInfo['openid'];
        $data['nickname'] =$userInfo['nickname'];
        $data['avatar'] =$userInfo['avatar'];
        $data['data']=json_encode($userInfo);
        return $data;
    }
    private function map_csdn_info($userInfo){
        $data=array();
        $data['openid'] = $userInfo['openid'];
        $data['nickname'] =$userInfo['nickname'];
        $data['avatar'] =$userInfo['avatar'];
        $data['data']=json_encode($userInfo);
        return $data;
    }
    private function map_coding_info($userInfo){
        $data=array();
        $data['openid'] = $userInfo['openid'];
        $data['nickname'] =$userInfo['nickname'];
        $data['avatar'] =$userInfo['avatar'];
        $data['data']=json_encode($userInfo);
        return $data;
    }
    private function map_baidu_info($userInfo){
        $data=array();
        $data['openid'] = $userInfo['openid'];
        $data['nickname'] =$userInfo['nickname'];
        $data['avatar'] =$userInfo['avatar'];
        $data['data']=json_encode($userInfo);
        return $data;
    }
    private function map_weibo_info($userInfo){
        $data=array();
        $data['openid'] = $userInfo['openid'];
        $data['nickname'] =$userInfo['nickname'];
        $data['avatar'] =$userInfo['avatar'];
        $data['data']=json_encode($userInfo);
        return $data;
    }
    private function map_qq_info($userInfo){
        $data=array();
        $data['openid'] = $userInfo['openid'];
        $data['unionid'] = $userInfo['unionid'];
        $data['nickname'] =$userInfo['nickname'];
        $data['avatar'] =$userInfo['figureurl_qq_1'];
        $data['gender'] = $userInfo['gender'];
        $data['data']=json_encode($userInfo);
        return $data;
    }
    
    /**
     * 微信登陆回调地址
     * 如果需要手机微信注册 请用这个方法 
     * 参考文档：http://mp.weixin.qq.com/wiki/9/01f711493b5a02f24b04365ac5d8fd95.html
     */
    private function map_wechat_info($userInfo)
    {
        $data=array();
        $data['openid'] = $userInfo['openid'];
        $data['unionid'] = $userInfo['unionid'];
        $data['nickname'] =$userInfo['nickname'];
        $data['avatar'] =$userInfo['headimgurl'];
        $data['is_follow'] = $userInfo['subscribe'];
        $data['gender'] = $userInfo['sex'];
        $data['province']= $userInfo['province'];
        $data['city']= $userInfo['city'];
        $data['country']= $userInfo['country'];
        $data['data']=json_encode($userInfo);
        if(!empty($userInfo['unionid'])){
            $sameAuth=MemberOauthModel::get(['unionid'=>$userInfo['unionid']]);
            if(!empty($sameAuth)){
                $data['member_id']=$sameAuth['member_id'];
            }
        }
        return $data;
    }

    public function getpassword(){
        $step=$this->request->get('step/d',1);
        $username=$this->request->post('username');
        $authtype=$this->request->post('authtype');

        if($step==2 || $step==3){
            $step--;
            if(empty($username))$this->error("请填写用户名");
            if(empty($authtype))$this->error("请选择认证方式");
            $user=Db::name('member')->where(array('username'=>$username))->find();
            if(empty($user)){
                $this->error("该用户不存在");
            }
            if(empty($user[$authtype.'_bind']))$this->error("认证方式无效");

            switch ($authtype){
                case 'email':
                    $this->assign('sendtoname',"邮箱");
                    break;
                case 'mobile':
                    $this->assign('sendtoname',"手机");
                    break;
            }
            $step++;
        }
        if($step==3){
            $step--;
            $sendto=$this->request->post('sendto');
            $code=$this->request->post('checkcode');
            $crow=Db::name('checkcode')->where(array('sendto'=>$sendto,'checkcode'=>$code,'is_check'=>0))->order('create_time DESC')->find();
            $time=time();
            if(!empty($crow) && $crow['create_time']>$time-60*5){
                Db::name('checkcode')->where(array('id' => $crow['id']))->update(array('is_check' => 1, 'check_at' => $time));
                session('passed',$username);
            }else{
                $this->error("验证码已失效");
            }


            $step++;
        }

        if($step==4){
            $step--;
            $passed=session('passed');
            if(empty($passed)){
                $this->error("非法操作");
            }
            $password=$this->request->post('password');
            $repassword=$this->request->post('repassword');

            if(empty($password))$this->error("请填写密码");
            if(strlen($password)<6 || strlen($password)>20)$this->error("密码长度 6-20");

            if($password != $repassword){
                $this->error("两次密码输入不一致");
            }
            $data['salt'] = random_str(8);
            $data['password'] = encode_password($password, $data['salt']);
            $data['update_time'] = time();
            if (Db::name('member')->where(array('username'=>$passed))->update($data)) {
                $this->success("密码设置成功",url('index/login/index'));
            }
        }

        $this->assign('username',$username);
        $this->assign('authtype',$authtype);
        $this->assign('step',$step);
        $this->assign('nextstep',$step+1);
        return $this->fetch();
    }
    public function checkusername(){
        Log::close();
        $username=$this->request->post('username');
        if(empty($username))$this->error("请填写用户名");
        $user=Db::name('member')->where(array('username'=>$username))->find();
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

    public function register($agent=''){
        $this->seo("会员注册");

        if(!empty($agent)){
            $amem=Db::name('Member')->where(array('is_agent'=>1,'agentcode'=>$agent))->find();
            if(!empty($amem)){
                session('agent',$amem['id']);
            }
        }

        if($this->request->isPost()){
            $data=$this->request->only('username,password,repassword,email,realname,mobile','post');

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
                $invite['use_at'] = time();
                Db::name('invite_code')->update($invite);
            }
            Db::commit();
            setLogin($model);
            $redirect=redirect()->restore();
            if(empty($redirect->getData())){
                $url=url('index/member/index');
            }else{
                $url=$redirect->getTargetUrl();
            }
            $this->success("注册成功",$url);
        }else{
            $this->assign('nocode',$this->config['m_invite']<1);
            return $this->fetch();
        }
    }

    public function checkunique($type='username'){
        Log::close();
        if(!in_array($type,array('username','email','mobile'))){
            $this->error('参数不合法');
        }
        $member=Db::name('member');
        $val=$this->request->get('value');
        $m=$member->where(array($type=>$val))->find();
        $json=array();
        $json['error']=0;
        if(!empty($m))$json['error']=1;
        return json($json);
    }

    public function verify(){
        $Verify = new \think\captcha\Captcha(array('seKey'=>'foreign'));
        $Verify->codeSet = '0123456789';
        $Verify->fontSize = 13;
        $Verify->length = 4;
        return $Verify->entry();
    }
    protected function check_verify($code){
        $verify = new \think\captcha\Captcha(array('seKey'=>'foreign'));
        return $verify->check($code);
    }

    public function logout()
    {
        clearLogin();
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
