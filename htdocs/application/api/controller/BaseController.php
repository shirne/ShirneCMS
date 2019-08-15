<?php

namespace app\api\controller;

use app\api\facade\MemberTokenFacade;
use think\Controller;
use think\Db;

define('ERROR_NEED_LOGIN',99);//需要登录
define('ERROR_LOGIN_FAILED',101);//登录失败
define('ERROR_NEED_REGISTER',109);//登录失败,需要绑定
define('ERROR_REGISTER_FAILED',111);//注册失败
define('ERROR_TOKEN_INVAILD',102);//token无效
define('ERROR_TOKEN_EXPIRE',103);//token过期
define('ERROR_REFRESH_TOKEN_INVAILD',105);//refresh_token失效

define('ERROR_NEED_OPENID',111);

/**
 * API基类.
 * Class BaseController
 * @package app\api\controller
 */
class BaseController extends Controller
{
    protected $token;
    protected $isLogin=false;
    protected $user;
    protected $input=array();
    protected $config=array();
    
    /**
     * API初始化
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function initialize(){
        parent::initialize();
        $this->config=getSettings();

        $this->input=$this->request->put();

        $this->checkLogin();
    }
    
    /**
     * 检查登录状态
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function checkLogin(){
        $this->token = $this->request->param('token');
        if(!empty($this->token)){
            $token=MemberTokenFacade::findToken($this->token);
            $errorno=ERROR_TOKEN_INVAILD;
            if(!empty($token)) {
                if($token['update_time']+$token['expire_in']>time()){
                    $this->user = Db::name('Member')->find($token['member_id']);
                }else{
                    $errorno=ERROR_TOKEN_EXPIRE;
                }
            }

            if(!empty($this->user)) {
                $this->isLogin=true;
            }else{
                $this->token=null;
                $this->error("登录失效",$errorno);
            }
        }
    }
    
    public function _empty(){
        $this->error('接口不存在',url('index/index/index'));
    }

    protected function get_param($key){
        if(isset($this->input[$key])){
            return $this->input[$key];
        }
        return $this->request->param($key);
    }
    protected function has_param($key){
        if(!isset($this->input[$key])){
            return $this->request->has($key);
        }
        return true;
    }

    protected function error($msg = '', $code = 0, $data = '', $wait = 3, array $header = [])
    {
        $this->response($data,$code,$msg)->send();
        exit;
    }

    protected function success($data = '', $code = 1, $msg = '', $wait = 3, array $header = [])
    {
        if(empty($msg) && is_string($data)){
            $msg=$data;
            $data=[];
        }
        $this->response($data,$code,$msg)->send();
        exit;
    }

    /**
     * ajax输出
     * @param $data
     * @param int $code
     * @param string $msg
     * @return \think\response\Json
     */
    protected function response($data,$code=1,$msg = ''){

        return json([
            'code' => $code,
            'msg'  => $msg,
            'time' => time(),
            'data' => $data,
        ]);
    }
}