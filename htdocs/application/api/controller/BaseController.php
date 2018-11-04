<?php

namespace app\api\controller;

use app\api\facade\MemberTokenModel;
use think\Controller;
use think\Db;

define('ERROR_LOGIN_FAILED',101);//登录失败
define('ERROR_REGISTER_FAILED',111);//注册失败
define('ERROR_TOKEN_INVAILD',102);//token无效
define('ERROR_TOKEN_EXPIRE',103);//token过期
define('ERROR_REFRESH_TOKEN_INVAILD',105);//refresh_token失效

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

    public function initialize(){
        parent::initialize();
        $this->config=getSettings();

        $format=$this->request->get('format','json');
        $data=file_get_contents('php://input');
        if(!empty($data)) {
            if ($format == 'xml') {
                $data=simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);
                if($data)$data = json_encode($data);
            }
            if (!empty($data)) {
                $this->input = json_decode($data, TRUE);
            }
            if(!is_array($this->input)){
                $this->input=array();
            }
        }

        $this->checkLogin();

    }

    public function checkLogin(){
        $this->token = $this->request->get('token');
        if(!empty($this->token)){
            $token=MemberTokenModel::findToken($this->token);
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

    protected function error($msg = '', $code = 0, $data = '', $wait = 3, array $header = [])
    {
        $this->result($data,$code,$msg);
    }

    protected function success($data = '', $code = 1, $msg = '', $wait = 3, array $header = [])
    {
        if(empty($msg) && is_string($data)){
            $msg=$data;
            $data=[];
        }
        $this->result($data,$code,$msg);
    }

    protected function response($data,$code=1){
        $this->result($data,$code);
    }
}