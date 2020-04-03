<?php

namespace app\api\controller;

use think\App;
use app\api\facade\MemberTokenFacade;
use app\api\middleware\AccessMiddleware;
use think\facade\Db;


/**
 * API基类.
 * Class BaseController
 * @package app\api\controller
 */
class BaseController
{
    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

    protected $token;
    protected $isLogin=false;
    protected $user;
    protected $input=array();
    protected $config=array();
    
    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
        $this->app     = $app;
        $this->request = $this->app->request;

        // 控制器初始化
        $this->initialize();
    }

    /**
     * API初始化
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function initialize(){
        parent::initialize();
        $this->config=getSettings();

        /**
         * @deprecated DO NOT use this property
         */
        $this->input=$this->request->put();

        $this->checkLogin();
    }
    
    
    /**
     * @return array
     */
    protected function userLevel(){
        if($this->isLogin) {
            $levels = getMemberLevels();
            if (isset($levels[$this->user['id']])) {
                return $levels[$this->user['id']];
            }
        }
        return [];
    }
    
    /**
     * 操作频率限制，防止垃圾数据及重复提交
     * @param int $seconds  限制频率时间（秒）
     * @param string $key   应用单独的key 默认使用全局
     * @param string $hashkey   未登录情况下，系统判断是否同一用户的依据
     */
    protected function check_submit_rate($seconds=2, $key='global', $hashkey=''){
        $cache_key = 'submit_'.$key.'_';
        if(empty($this->token)){
            if(empty($hashkey))$hashkey = $this->request->ip().$this->request->server('user_agent');
            $cache_key .= md5($hashkey);
        }else {
            $cache_key .= $this->token;
        }
        $lasttime = cache($cache_key);
        $curtime=time();
        if(!$lasttime || $lasttime + $seconds < $curtime){
            cache($cache_key, $curtime, ['expire'=>$seconds]);
        }else{
            $this->error('操作过于频繁');
        }
    }
    
    /**
     * 检查登录状态
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function checkLogin(){
        if($this->request->isLogin){
            $this->token = $this->request->token;
            $this->isLogin = $this->request->isLogin;
            $this->user = $this->request->user;
        }elseif($this->request->auth_error){
            $this->error("登录失效",$this->request->auth_error);
        }
    }
    
    public function _empty(){
        $static_file=DOC_ROOT.DIRECTORY_SEPARATOR.$this->request->action(true);
        if(file_exists($static_file)){
            exit(file_get_contents($static_file));
        }
        $this->error('接口不存在');
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
        ], 200, AccessMiddleware::$acrossHeaders);
    }
}