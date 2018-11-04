<?php
/**
 * 授权相关操作.
 * User: shirne
 * Date: 2018/3/21
 * Time: 7:30
 */

namespace app\api\Controller;

use app\api\facade\MemberTokenModel;
use app\common\model\MemberModel;
use app\common\model\MemberOauthModel;
use sdk\WechatAuth;
use think\Db;

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
                $token=MemberTokenModel::createToken($member['id']);
                if(!empty($token)) {
                    $this->response($token);
                }
            }
        }

        $this->error('登录失败',ERROR_LOGIN_FAILED);
    }

    public function wxLogin(){
        $code=$this->input['code'];
        $weauth=new WechatAuth(array('appid'=>$this->config['weapp_appid'],'appsecret'=>$this->config['weapp_appsecret']));
        $session=$weauth->getOauthAccessToken($code);
        if(empty($session) || empty($session['openid'])){
            $this->error('登录失败',ERROR_LOGIN_FAILED);
        }

        $rowData=$this->input['rawData'];
        if(!empty($rowData)){
            $signature=$this->input['signature'];
            if(sha1($rowData.$session['session_key'])==$signature) {
                $userinfo = json_decode($rowData, TRUE);
            }
        }
        $type='wxapp';

        $condition=array('type'=>$type,'openid'=>$session['openid']);
        $oauth=MemberOauthModel::get($condition);
        if(!empty($oauth))$member=MemberModel::get($oauth['member_id']);
        if(empty($member)){
            $register=getSetting('m_register');
            if($register=='1'){
                $this->error('登录失败',ERROR_LOGIN_FAILED);
            }elseif(!empty($userinfo)){
                //自动注册
                $data=$this->wxMapdata($userinfo,$rowData);
                $data['openid']=$session['openid'];
                $data['type']=$type;
                if(!empty($session['unionid']))$data['unionid']=$session['unionid'];

                $member_id=MemberModel::create(array(
                    'username'=>'',
                    'realname'=>$data['nickname'],
                    'avatar'=>$data['avatar'],
                    'gender'=>$data['gender'],
                    'city'=>$data['city']
                ));
                if($member_id){
                    $data['member_id']=$member_id;
                    if(empty($oauth)){
                        MemberOauthModel::create($data);
                    }else{
                        MemberOauthModel::update($data);
                    }
                }else{
                    $this->error('登录失败',ERROR_LOGIN_FAILED);
                }
                $member=MemberModel::get($member_id);
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

        $token=MemberTokenModel::createToken($member['id']);
        if(!empty($token)) {
            $this->response($token);
        }
        $this->error('登录失败',ERROR_LOGIN_FAILED);
    }
    private function wxMapdata($userinfo,$rowData){
        return array(
            'data'=>$rowData,
            'is_follow'=>0,
            'nickname'=>$userinfo['nickName'],
            'gender'=>$userinfo['gender'],
            'avatar'=>$userinfo['avatarUrl'],
            'city'=>$userinfo['city'],
            'province'=>$userinfo['province'],
            'country'=>$userinfo['country'],
            'language'=>$userinfo['language']
        );
    }

    public function refresh(){
        $refreshToken=$this->input['refresh_token'];
        if(!empty($refreshToken)){
            $tokenModel=new MemberTokenModel();
            $token=$tokenModel->refreshToken($refreshToken);
            if(!empty($token)) {
                $this->response($token);
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