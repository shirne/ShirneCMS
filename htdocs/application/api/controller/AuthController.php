<?php
/**
 * 授权相关操作.
 * User: shirne
 * Date: 2018/3/21
 * Time: 7:30
 */

namespace app\api\Controller;

use app\api\facade\MemberTokenFacade;
use app\common\model\MemberModel;
use app\common\model\MemberOauthModel;
use app\common\model\WechatModel;
use EasyWeChat\Factory;
use think\Db;
use think\facade\Env;

class AuthController extends BaseController
{
    public function initialize(){
        parent::initialize();
    }

    public function login(){
        $username=$this->input['username'];
        $password=$this->input['password'];
        if(empty($username) || empty($password)){
            $this->error('请填写登录账号及密码',ERROR_LOGIN_FAILED);
        }
        $member = Db::name('Member')->where('username',$username)->find();
        if(!empty($member)){
            if(compare_password($member,$password)){
                $token=MemberTokenFacade::createToken($member['id']);
                if(!empty($token)) {
                    return $this->response($token);
                }
            }
        }

        $this->error('登录失败',ERROR_LOGIN_FAILED);
    }

    /**
     * 微信小程序登录
     * @return \think\response\Json
     */
    public function wxLogin(){
        $wechat_id=$this->input['wxid'];
        $code=$this->input['code'];
        $agent=isset($this->input['agent'])?$this->input['agent']:'';
        $wechat=Db::name('wechat')->where('type','wechat')
            ->where('id|hash',$wechat_id)->find();
        if(empty($wechat)){
            $this->error('服务器配置错误',ERROR_LOGIN_FAILED);
        }
        $options=WechatModel::to_config($wechat);
        switch ($wechat['account_type']) {
            case 'wechat':
            case 'subscribe':
            case 'service':
                $this->error('该接口不支持公众号登录',ERROR_LOGIN_FAILED);
                break;
            case 'miniprogram':
            case 'minigame':
                $weapp=Factory::miniProgram($options);
                break;
            default:
                $this->error('配置错误',ERROR_LOGIN_FAILED);
                break;
        }
        //调试模式允许mock登录
        if($wechat['is_debug'] && $code=='the code is a mock one'){
            $rowData = $this->input['rawData'];
            $userinfo = json_decode($rowData, TRUE);
            $session=['openid'=>md5($userinfo['nickName'])];
        }else {
            $session = $weapp->auth->session($code);
            if (empty($session) || empty($session['openid'])) {
                $this->error('登录失败', ERROR_LOGIN_FAILED);
            }

            $rowData = $this->input['rawData'];
            if (!empty($rowData)) {
                $signature = $this->input['signature'];
                if (sha1($rowData . $session['session_key']) == $signature) {
                    $userinfo = json_decode($rowData, TRUE);
                }
            }
        }
        $type='wechat';

        $condition=array('type'=>$type,'openid'=>$session['openid']);
        $oauth=MemberOauthModel::where($condition)->find();
        if(!empty($oauth) && $oauth['member_id'])$member=MemberModel::where('id',$oauth['member_id'])->find();
        if(empty($member)){
            $register=getSetting('m_register');
            if($this->isLogin){
                $member=$this->user;
            }elseif($register=='1'){
                $this->error('登录失败', ERROR_NEED_REGISTER);
            }
            if(!empty($userinfo)){
                //自动注册
                $data=$this->wxMapdata($userinfo,$rowData);
                $data['openid']=$session['openid'];
                $data['type']=$type;
                if(!empty($session['unionid']))$data['unionid']=$session['unionid'];

                if(empty($member)) {
                    $member = MemberModel::createFromOauth($data);
                }
                if($member['id']){
                    $data['member_id']=$member['id'];
                    if(empty($oauth)){
                        MemberOauthModel::create($data);
                    }else{
                        MemberOauthModel::update($data);
                    }
                }else{
                    $this->error('登录失败',ERROR_LOGIN_FAILED);
                }
            }else{
                $this->error('登录授权失败',ERROR_LOGIN_FAILED);
            }
        }elseif(!empty($userinfo)){
            //更新资料
            $data=$this->wxMapdata($userinfo,$rowData);
            if(!empty($session['unionid']))$data['unionid']=$session['unionid'];
            MemberOauthModel::update($data,$condition);
            $updata=array();
            $updata['gender']=$data['gender'];
            $updata['city']=$data['city'];
            if($member['realname']==$oauth['nickname'])$updata['realname']=$data['nickname'];
            if($member['avatar']==$oauth['avatar'])$updata['avatar']=$data['avatar'];
            if(!empty($updata)){
                MemberModel::update($updata,array('id'=>$member['id']));
            }

        }

        $token=MemberTokenFacade::createToken($member['id']);
        if(!empty($token)) {
            return $this->response($token);
        }
        $this->error('登录失败',ERROR_LOGIN_FAILED);
    }

    /**
     * 第三方登录数据转换
     * @param $userinfo
     * @param $rowData
     * @return array
     */
    private function wxMapdata($userinfo,$rowData){
        return array(
            'data'=>$rowData,
            'is_follow'=>0,
            'nickname'=>$userinfo['nickName'],
            'gender'=>$userinfo['gender'],
            'avatar'=>$userinfo['avatarUrl'],
            'city'=>$userinfo['city'],
            'province'=>$userinfo['province'],
            'country'=>isset($userinfo['country'])?$userinfo['country']:'',
            'language'=>isset($userinfo['language'])?$userinfo['language']:''
        );
    }

    public function refresh(){
        $refreshToken=$this->input['refresh_token'];
        if(!empty($refreshToken)){
            $token=MemberTokenFacade::refreshToken($refreshToken);
            if(!empty($token)) {
                return $this->response($token);
            }
        }
        $this->error('刷新失败',ERROR_REFRESH_TOKEN_INVAILD);
    }

    /**
     * 注册会员
     */
    public function register(){

    }

}