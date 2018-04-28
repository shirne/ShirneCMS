<?php
/**
 * 授权相关操作.
 * User: shirne
 * Date: 2018/3/21
 * Time: 7:30
 */

namespace app\api\Controller;

use Extend\WechatAuth;

class AuthController extends BaseController
{
    public function _initialize(){
        parent::_initialize();
    }

    public function login(){
        $username=$this->input['username'];
        $password=$this->input['password'];
        if(empty($username) || empty($password)){
            $this->response('请填写登录账号及密码',ERROR_LOGIN_FAILED);
        }
        $member = M('member')->where(array('username'=>$username))->find();
        if(!empty($member)){
            if(compare_password($member,$password)){
                $token=D('Token')->createToken($member['id']);
                if(!empty($token)) {
                    $this->response($token);
                }
            }
        }

        $this->response('登录失败',ERROR_LOGIN_FAILED);
    }

    public function wxLogin(){
        $code=$this->input['code'];
        $weauth=new WechatAuth(array('appid'=>$this->config['weapp_appid'],'appsecret'=>$this->config['weapp_appsecret']));
        $session=$weauth->getSession($code);
        if(empty($session) || empty($session['openid'])){
            $this->response('登录失败',ERROR_LOGIN_FAILED);
        }

        $rowData=$this->input['rawData'];
        if(!empty($rowData)){
            $signature=$this->input['signature'];
            if(sha1($rowData.$session['session_key'])==$signature) {
                $userinfo = json_decode($rowData, TRUE);
            }
        }
        $type='wxapp';

        $condition=array('member_oauth.type'=>$type,'member_oauth.openid'=>$session['openid']);
        $member=D('OAhthView')->where($condition)->find();
        if(empty($member)){
            $register=getSetting('m_register');
            if($register=='1'){
                $this->response('登录失败',ERROR_LOGIN_FAILED);
            }elseif(!empty($userinfo)){
                //自动注册
                $data=$this->wxMapdata($userinfo,$rowData);
                $data['openid']=$session['openid'];
                $data['type']=$type;
                if(!empty($session['unionid']))$data['unionid']=$session['unionid'];
                $member_id=D('Member')->add(array(
                    'username'=>'',
                    'realname'=>$data['nickname'],
                    'avatar'=>$data['avatar'],
                    'gender'=>$data['gender'],
                    'city'=>$data['city']
                ));
                if($member_id){
                    $data['member_id']=$member_id;
                    D('MemberOauth')->add($data);
                }else{
                    $this->response('登录失败',ERROR_LOGIN_FAILED);
                }
                $member=D('OAhthView')->where($condition)->find();
            }
        }elseif(!empty($userinfo)){
            //更新资料
            $data=$this->wxMapdata($userinfo,$rowData);
            if(!empty($session['unionid']))$data['unionid']=$session['unionid'];
            D('MemberOauth')->alias('member_oauth')->where($condition)->save($data);
            $updata=array();
            $updata['gender']=$data['gender'];
            $updata['city']=$data['city'];
            if($member['realname']==$member['auth_nickname'])$updata['realname']=$data['nickname'];
            if($member['avatar']==$member['auth_avatar'])$updata['avatar']=$data['avatar'];
            if(!empty($updata)){
                M('member')->where(array('id'=>$member['id']))->save($updata);
            }

        }
        $token=D('Token')->createToken($member['id']);
        if(!empty($token)) {
            $this->response($token);
        }
        $this->response('登录失败',ERROR_LOGIN_FAILED);
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
            $token=D('Token')->refreshToken($refreshToken);
            if(!empty($token)) {
                $this->response($token);
            }
        }
        $this->response('刷新失败',ERROR_REFRESH_TOKEN_INVAILD);
    }


}