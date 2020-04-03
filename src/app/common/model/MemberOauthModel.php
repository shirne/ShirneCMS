<?php

namespace app\common\model;


use app\common\core\BaseModel;
use think\facade\Db;

/**
 * Class MemberOauthModel
 * @package app\common\model
 */
class MemberOauthModel extends BaseModel
{
    protected $name = 'member_oauth';
    protected $autoWriteTimestamp = true;

    public static function checkUser($data, $account){
        $user=static::where('openid',$data['openid'])->find();
        if(empty($data['data'])) {
            $data = MemberOauthModel::mapUserInfo($data);
        }
    
        $data['type'] = $account['account_type'];
        $data['type_id'] = $account['id'];
        if(empty($user['member_id']) && !empty($data['unionid'])){
            $sameuser=Db::name('memberOauth')->where('unionid',$data['unionid'])->find();
            if(!empty($sameuser['member_id'])){
                $data['member_id']=$sameuser['member_id'];
            }
        }
        if(empty($user)){
            if(!isset($data['member_id']))$data['member_id']=0;
            
            $data = static::create($data);
            $data['is_new']=1;
        }else{
            $user->save($data);
        }
        if($user['member_id']==0 && getSetting('m_register') != '1'){
            $member = MemberModel::createFromOauth($user,session('agent'));
            $user->save(['member_id' => $member['id']]);
        }
        
        return $user;
    }

    public static function getAccountsByMemberAndType($member_id, $type = 'service')
    {
        return Db::view('wechat','*')
        ->view('memberOauth',['member_id','openid'],'memberOauth.type_id=wechat.id')
        ->where('memberOauth.type',$type )
        ->where('memberOauth.member_id',$member_id )
        ->where('wechat.account_type',$type )
        ->select();
    }
    
    public static function mapUserInfo($userInfo){
        $data=[];

        $data['openid'] = $userInfo['openid'];
        $data['nickname'] =$userInfo['nickname'];
        $data['name'] =$userInfo['nickname'];
        $data['avatar'] =$userInfo['headimgurl'];

        $data['gender'] = empty($userInfo['gender'])?0:$userInfo['gender'];
        $data['unionid'] = empty($userInfo['unionid'])?'':$userInfo['unionid'];
        $data['data']=json_encode($userInfo);

        $data['city'] =$userInfo['city'];
        $data['province'] =$userInfo['province'];
        $data['country'] =$userInfo['country'];
        $data['language'] =$userInfo['language'];
        $data['subscribe_time']=$userInfo['subscribe_time'];
        if($data['subscribe_time']>0){
            $data['is_follow']=1;
        }

        return $data;
    }
}