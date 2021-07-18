<?php

namespace app\api\controller;

use app\api\facade\MemberTokenFacade;
use app\api\middleware\AccessMiddleware;
use InvalidArgumentException;
use shirne\common\ValidateHelper;
use think\Controller;
use think\Db;


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

        /**
         * @deprecated DO NOT use this property
         */
        $this->input=$this->request->put();

        $this->checkLogin();
    }
    
    
    /**
     * 获取当前登录用户的级别信息
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
     * 手机验证限制
     * @param mixed $mobile 
     * @return bool 
     * @throws InvalidArgumentException 
     */
    protected function mobile_verify_limit($mobile){
        if(!ValidateHelper::isMobile($mobile)){
            $this->error('手机号码格式错误');
            return false;
        }
        $sended = cache('mobile_limit_'.$mobile);
        if($sended){
            return false;
        }
        $count = cache('mobile_limit_hour_'.$mobile);
        if($count >= 5){
            return false;
        }
        return true;
    }

    /**
     * 增加验证次数
     * @param mixed $mobile 
     * @return void 
     */
    protected function mobile_verify_add($mobile){
        cache('mobile_limit_'.$mobile,1,['expire'=>50]);
        $counted = max(0,cache('mobile_limit_hour_'.$mobile));
        cache('mobile_limit_hour_'.$mobile,$counted+1,['expire'=>60*60]);
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
    
    /**
     * 空操作输出
     * @return void 
     * @throws InvalidArgumentException 
     */
    public function _empty(){
        $static_file=DOC_ROOT.DIRECTORY_SEPARATOR.$this->request->action(true);
        if(file_exists($static_file)){
            exit(file_get_contents($static_file));
        }
        $this->error('接口不存在');
    }

    /**
     * 输出API错误信息
     * @param string $msg 
     * @param string|int $code 
     * @param mixed $data 
     * @param int $wait 
     * @param array $header 
     * @return void 
     */
    protected function error($msg = '', $code = 0, $data = '', $wait = 3, array $header = [])
    {
        $this->response($data,$code,$msg)->send();
        exit;
    }

    /**
     * 输出API成功数据
     * @param mixed $data 
     * @param string|int $code 
     * @param mixed $msg 
     * @param int $wait 
     * @param array $header 
     * @return void 
     * @throws InvalidArgumentException 
     */
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
     * 输出分页列表数据
     * @param Paginator $lists 
     * @param array $exts 
     * @return Json 
     */
    protected function respList($lists, $exts = []){
        $result = array_merge([
            'lists'=>$lists->items(),
            'page'=>$lists->currentPage(),
            'count'=>$lists->total(),
            'total_page'=>$lists->lastPage(),
        ], $exts);
        return $this->response($result);
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