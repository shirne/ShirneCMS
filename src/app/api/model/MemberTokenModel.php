<?php

namespace app\api\model;

use app\common\core\BaseModel;
use think\facade\Log;

/**
 * Class MemberTokenModel
 * @package app\api\model
 */
class MemberTokenModel extends BaseModel
{
    protected $name = 'member_token';
    protected $pk='token_id';
    private $hash='sw4GomU4LXvYqcaLctXCLK43eRcob';
    private $expire=1800;

    protected $autoWriteTimestamp = true;
    
    public function __construct($data = [])
    {
        parent::__construct($data);
        $config_expire = config('app.token_expire');
        if($config_expire)$this->expire = $config_expire;
    }
    
    protected function getToken($memid,$hash=''){
        if(empty($hash))$hash=$this->hash;
        return md5($hash.date('YmdHis').microtime().str_pad($memid,11));
    }

    /**
     * token数据转换
     * @param $data
     * @param bool $response 是否用于输出
     * @return array
     */
    protected function mapToken($data,$response=true){
        $token = array(
            'member_id'=>$data['member_id'],
            'token'=>$data['token'],
            'expire_in'=>$data['expire_in'],
            'refresh_token'=>$data['refresh_token']
        );
        if(!$response){
            $token['member_id']=$data['member_id'];
            $token['platform']=$data['platform'];
            $token['appid']=$data['appid'];
            $token['create_time']=$data['create_time'];
            $token['update_time']=$data['update_time'];
        }
        return $token;
    }
    public function findToken($token, $response=false){
        $token=$this->where('token',$token)->find();
        if(empty($token)){
            return false;
        }
        return $this->mapToken($token,$response);
    }
    public function createToken($member_id,$platform='app', $appid=''){
        $token=$this->where('member_id',$member_id)
            ->where('platform',$platform)
            ->where('appid',$appid)
            ->find();
        $data=array(
            'token'=>$this->getToken($member_id),
            'expire_in'=>$this->expire
        );
        $data['refresh_token']=$this->getToken($member_id,str_shuffle($data['token']));
        if(empty($token)){
            $data['platform']=$platform;
            $data['appid']=$appid;
            $data['member_id']=$member_id;
            $added=static::create($data);
            if(!$added['token_id']){
                Log::record('Token insert error:'.$this->getError());
            }
        }else{
            static::update($data,['member_id'=>$member_id,'platform'=>$platform,'appid'=>$appid]);
        }
        return $this->findToken($data['token'],true);
    }
    public function refreshToken($refresh){
        $token=$this->where('refresh_token',$refresh)->find();
        if(empty($token)){
            return false;
        }

        $data=array(
            'token'=>$this->getToken($token['member_id']),
            'expire_in'=>$this->expire
        );
        $data['refresh_token']=$this->getToken($token['member_id'],str_shuffle($data['token']));

        static::update($data,['member_id'=>$token['member_id']]);

        return $this->findToken($data['token'],true);
    }

    public function clearToken($token){
        $token=$this->where('token',$token)->find();
        if(empty($token)){
            return false;
        }

        $data=array(
            'token'=>'',
            'expire_in'=>0
        );
        $data['refresh_token']='';

        return static::update($data,['member_id'=>$token['member_id']]);
    }
}