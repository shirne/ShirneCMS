<?php
namespace app\admin\controller;

use extcore\traits\Verify;
use think\Db;
use think\Exception;

/**
 * 后台登录
 * Class LoginController
 * @package app\admin\controller
 */
class LoginController extends BaseController
{

    public function initialize()
    {
        parent::initialize();
        
        $action=strtolower($this->request->action());
        if($this->mid && $action != 'logout'){
            $this->success(lang('You\'r already logged in!'),url('admin/index/index'));
        }
    }

    /**
     * 登陆主页
     * @return mixed
     * @throws \Throwable
     */
    public function index(){
        $this->assign('config',getSettings());
        return $this->fetch();
    }

    /**
     * 登陆验证
     */
    public function login(){
        if(!$this->request->isPost())$this->error(lang('Bad Request!'));
        $member = Db::name('Manager');
        $username =$this->request->post('username','','trim');
        $password =$this->request->post('password');

        if(empty($username) || empty($password)){
            $this->error(lang('Please fill in the login field!'));
        }

        //验证验证码是否正确
        if(!($this->check_verify($this->request->post()))){
            $this->error(lang('Verify code error!'));
        }

        $sess_key='back_login_error';
        $error_count=session($sess_key);
        if(is_null($error_count)){
            $error_count=0;
        }elseif($error_count>5){
            $this->error(lang('Login error of too many times!'));
        }

        $ip=$this->request->ip();
        $cache_key='back_login_error_'.str_replace(['.',':'],['_','-'],$ip);
        $iperror_count=cache($cache_key);
        if(is_null($iperror_count)){
            $iperror_count=0;
        }elseif($iperror_count>10){
            $this->error(lang('Login error of too many times!'));
        }

        //验证账号密码是否正确
        $user = $member->where('username',$username)->find();

        if(empty($user) || $user['password'] !== encode_password($password,$user['salt'])) {

            $error_count++;
            $iperror_count++;
            session($sess_key,$error_count);
            cache($cache_key,$iperror_count,['expire'=>3600]);

            if(!empty($user)){
                //登录日志
                user_log($user['id'],'login',0,['Incorrect password: %s',$password],'manager');
            }
            $this->error(lang('Account or password incorrect!')) ;
        }

        //登录成功清除限制
        session($sess_key,null);
        cache($cache_key,null);

        //验证账户是否被禁用
        if($user['status'] == 0){
            user_log($user['id'],'login',0,lang('Account is disabled!') ,'manager');
            $this->error(lang('Account is disabled, pls contact the super master!'));
        }

        //密码复杂度检查
        check_password($password);

        setLogin($user);

        $this->success(lang('Login success!'),url('Index/index'));
    }

    use Verify;

    /**
     * 验证码
     * @return \think\Response
     */
    public function verify(){
        return $this->verify_auto('backend',getSettings());
    }

    protected function check_verify($data){
        return $this->check_verify_auto('backend',$data,getSettings());
    }

    /**
     * 退出登录
     */
    public function logout(){
        clearLogin();
        $this->redirect(url('index'));
    }
}