<?php

namespace app\common\service;
use extcore\traits\Email;
use think\Db;
use shirne\third\UmsHttp;

/**
 * Class CheckcodeService
 * @package app\common\service
 */
class CheckcodeService
{
    use Email;

    protected $errMsg;
    protected $limits=[
        'email'=>[],
        'mobile'=>[
            'ip_limit'=>10,
            'item_limit'=>5,
            'second_limit'=>120
        ]
    ];

    public function getError(){
        return $this->errMsg;
    }

    public function sendCode($type,$sendto){
        $ip=request()->ip();
        $limit=isset($this->limits[$type])?$this->limits[$type]:[];
        if(!empty($limit)) {

            //根据ip地址限制发送次数
            $ipcount = Db::name('checkcodeLimit')->where('type', 'ip')
                ->where('key', $ip)->find();
            if (!empty($ipcount)) {
                if ($ipcount['create_time'] < strtotime(date('Y-m-d'))) {
                    Db::name('checkcodeLimit')->where('type', 'ip')->where('key', $ip)
                        ->update(['create_time' => time(), 'count' => 0]);
                } else {
                    if ($ipcount['count'] >= $limit['ip_limit']) {
                        $this->errMsg='验证码发送过于频繁';
                        return false;
                    }
                }
            }

            //根据手机号码限制发送次数
            $phonecount = Db::name('checkcodeLimit')->where('type', $type)
                ->where('key', $sendto)->find();
            if (!empty($phonecount)) {
                if ($phonecount['create_time'] < strtotime(date('Y-m-d'))) {
                    Db::name('checkcodeLimit')->where('type', 'mobile')->where('key', $sendto)
                        ->update(['create_time' => time(), 'count' => 0]);
                } else {
                    if ($phonecount['count'] >= $limit['item_limit']) {
                        $this->errMsg='验证码发送过于频繁';
                        return false;
                    }
                }
            }
        }


        $sec_limit=isset($limit['second_limit'])?$limit['second_limit']:0;
        switch ($type){
            case 'mobile':
                $result=$this->sendMobileCode($sendto,$sec_limit);
                break;
            case 'email':
                $result=$this->sendEmailCode($sendto,$sec_limit);
                break;
            default:
                return false;
        }
        if($result){

            if(!empty($limit)) {
                if (empty($ipcount)) {
                    Db::name('checkcodeLimit')->insert([
                        'type' => 'ip',
                        'key' => $ip,
                        'create_time' => time(),
                        'count' => 1
                    ]);
                }else{
                    Db::name('checkcodeLimit')->where('type', 'ip')->where('key', $ip)
                        ->setInc('count', 1);
                }

                if (empty($phonecount)) {
                    Db::name('checkcodeLimit')->insert([
                        'type' => $type,
                        'key' => $sendto,
                        'create_time' => time(),
                        'count' => 1
                    ]);
                }else{
                    Db::name('checkcodeLimit')->where('type', $type)->where('key', $sendto)
                        ->setInc('count', 1);
                }
            }
            return true;
        }

        return false;
    }

    protected function sendMobileCode($mobile,$timeLimit){
        $exist=Db::name('Checkcode')->where('type',0)
            ->where('sendto',$mobile)
            ->where('is_check',0)->find();
        $newcode=random_str(6, 'number');
        if(empty($exist)) {
            $data = [
                'type' => 0,
                'sendto' => $mobile,
                'code' => $newcode,
                'create_time' => time(),
                'is_check' => 0
            ];
            Db::name('Checkcode')->insert($data);
        }else{

            //验证发送时间间隔
            if(time()-$exist['create_time']<$timeLimit){
                $this->errMsg='验证码发送过于频繁';
                return false;
            }

            Db::name('Checkcode')->where('type',0)->where('sendto',$mobile)
                ->update(['code'=>$newcode,'create_time'=>time()]);
        }
        @session_write_close();
        $content="您本次验证码为{$newcode}仅用于会员注册。请在10分钟内使用！";
        $sms=new UmsHttp(getSettings());
        $sended=$sms->send($mobile,$content);
        return $sended;
    }

    protected function sendEmailCode($email,$timeLimit){
        $exist=Db::name('Checkcode')->where('type',1)
            ->where('sendto',$email)
            ->where('is_check',0)->find();
        $newcode=random_str(8, 'string');
        if(empty($exist)) {
            $data = [
                'type' => 1,
                'sendto' => $email,
                'code' => $newcode,
                'create_time' => time(),
                'is_check' => 0
            ];
            Db::name('Checkcode')->insert($data);
        }else{

            //验证发送时间间隔
            if(time()-$exist['create_time']<$timeLimit){
                $this->errMsg='验证码发送过于频繁';
                return false;
            }

            Db::name('Checkcode')->where('type',1)->where('sendto',$email)
                ->update(['code'=>$newcode,'create_time'=>time()]);
        }
        @session_write_close();
        $content="您本次验证码为{$newcode}仅用于会员注册。请在10分钟内使用！";
        $settings=getSettings();
        $this->setEmailConfig($settings);
        $sended=$this->sendEmail($email,$settings['site-webname'].'-验证码',$content);
        return $sended;
    }

    public function verifyCode($sendto,$code){
        $crow = Db::name('checkcode')
            ->where('sendto' , $sendto)
            ->where('checkcode', $code)
            ->where( 'is_check', 0)
            ->order('create_time DESC')->find();
        $time = time();
        if (!empty($crow) && $crow['create_time'] > $time - 60 * 5) {
            Db::name('checkcode')->where('id', $crow['id'])->update(array('is_check' => 1, 'check_at' => $time));
            return true;
        }
        return false;
    }
}