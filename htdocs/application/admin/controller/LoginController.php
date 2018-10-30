<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;

/**
 * 后台登录
 * Class LoginController
 * @package app\admin\controller
 */
class LoginController extends Controller {

    /**
     * 登陆主页
     * @return mixed
     */
    public function index(){
        return $this->fetch();
    }

    /**
     * 登陆验证
     */
    public function login(){
        if(!$this->request->isPost())$this->error("非法请求");
        $member = Db::name('Manager');
        $username =$this->request->post('username','','trim');
        $password =$this->request->post('password');
        $code = $this->request->post('verify','','strtolower');

        if(empty($username) || empty($password)){
            $this->error('请填写登录信息');
        }

        //验证验证码是否正确
        if(!($this->check_verify($code))){
            $this->error('验证码错误');
        }

        $sess_key='back_login_error';
        $error_count=session($sess_key);
        if(is_null($error_count)){
            $error_count=0;
        }elseif($error_count>5){
            $this->error('登录错误次数过多');
        }

        $ip=$this->request->ip();
        $cache_key='back_login_error_'.str_replace(['.',':'],['_','-'],$ip);
        $iperror_count=cache($cache_key);
        if(is_null($iperror_count)){
            $iperror_count=0;
        }elseif($iperror_count>10){
            $this->error('登录错误次数过多');
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
                user_log($user['id'],'login',0,'密码错误:'.$password,'manager');
            }
            $this->error('账号或密码错误 :(') ;
        }

        //登录成功清除限制
        session($sess_key,null);
        cache($cache_key,null);

        //验证账户是否被禁用
        if($user['status'] == 0){
            user_log($user['id'],'login',0,'账号已禁用' ,'manager');
            $this->error('账号被禁用，请联系超级管理员 :(') ;
        }

        setLogin($user);

        $this->success("登陆成功",url('Index/index'));
    }

    /**
     * 验证码
     * @return \think\Response
     */
    public function verify(){
        $verify = new \think\captcha\Captcha();
        //$Verify->codeSet = '0123456789';
        $verify->seKey=config('session.sec_key');
        $verify->fontSize = 13;
        $verify->length = 4;
        return $verify->entry('backend');
    }
    protected function check_verify($code){
        $verify = new \think\captcha\Captcha();
        $verify->seKey=config('session.sec_key');
        return $verify->check($code,'backend');
    }

    /**
     * 退出登录
     */
    public function logout(){
        clearLogin();
        $this->redirect(url('index'));
    }
}