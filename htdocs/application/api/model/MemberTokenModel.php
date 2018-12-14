<?php

namespace app\api\model;

use app\common\model\BaseModel;
use think\facade\Log;

/**
 * Class MemberTokenModel
 * @package app\api\model
 */
class MemberTokenModel extends BaseModel
{
    private $hash='sw4GomU4LXvYqcaLctXCLK43eRcob';
    private $expire=720;

    protected $autoWriteTimestamp = true;

    protected function getToken($memid,$hash=''){
        if(empty($hash))$hash=$this->hash;
        return md5($hash.date('YmdHis').microtime().str_pad($memid,11));
    }
    protected function mapToken($data,$response=true){
        $token = array(
            'member_id'=>$data['member_id'],
            'token'=>$data['token'],
            'expire_in'=>$data['expire_in'],
            'refresh_token'=>$data['refresh_token']
        );
        if(!$response){
            $token['member_id']=$data['member_id'];
            $token['create_time']=$data['create_time'];
            $token['update_time']=$data['update_time'];
        }
        return $token;
    }
    public function findToken($token){
        $token=$this->where('token',$token)->find();
        if(empty($token)){
            return false;
        }
        return $this->mapToken($token,false);
    }
    public function createToken($member_id){
        $token=$this->where('member_id',$member_id)->find();
        $data=array(
            'token'=>$this->getToken($member_id),
            'expire_in'=>$this->expire
        );
        $data['refresh_token']=$this->getToken($member_id,str_shuffle($data['token']));
        if(empty($token)){
            $data['member_id']=$member_id;
            $added=static::create($data);
            if(!$added['token_id']){
                Log::record('Token insert error:'.$this->getError());
            }
        }else{
            static::update($data,['member_id'=>$member_id]);
        }
        return $this->mapToken($this->where('member_id',$member_id)->find());
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

        return $this->mapToken($this->where('member_id',$token['member_id'])->find());
    }
}