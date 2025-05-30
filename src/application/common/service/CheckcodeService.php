<?php

namespace app\common\service;

use extcore\traits\Email;
use shirne\common\ValidateHelper;
use shirne\third\Aliyun;
use think\Db;

/**
 * Class CheckcodeService
 * @package app\common\service
 */
class CheckcodeService extends BaseService
{
    use Email;

    protected $limits = [
        'email' => [],
        'mobile' => [
            'ip_limit' => 10,
            'item_limit' => 5,
            'second_limit' => 120
        ]
    ];

    public function sendCode($type, $sendto, $codetype = 'verify')
    {
        $ip = request()->ip();
        $limit = isset($this->limits[$type]) ? $this->limits[$type] : [];
        if (!empty($limit)) {

            //根据ip地址限制发送次数
            $ipcount = Db::name('checkcodeLimit')->where('type', 'ip')
                ->where('key', $ip)->find();
            if (!empty($ipcount)) {
                if ($ipcount['create_time'] < strtotime(date('Y-m-d'))) {
                    Db::name('checkcodeLimit')->where('type', 'ip')->where('key', $ip)
                        ->update(['create_time' => time(), 'count' => 0]);
                } else {
                    if ($ipcount['count'] >= $limit['ip_limit']) {
                        $this->setError('验证码发送过于频繁');
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
                        $this->setError('验证码发送过于频繁');
                        return false;
                    }
                }
            }
        }


        $sec_limit = isset($limit['second_limit']) ? $limit['second_limit'] : 0;
        switch ($type) {
            case 'mobile':
                $result = $this->sendMobileCode($sendto, $sec_limit, $codetype);
                break;
            case 'email':
                $result = $this->sendEmailCode($sendto, $sec_limit);
                break;
            default:
                return false;
        }
        if ($result) {

            if (!empty($limit)) {
                if (empty($ipcount)) {
                    Db::name('checkcodeLimit')->insert([
                        'type' => 'ip',
                        'key' => $ip,
                        'create_time' => time(),
                        'count' => 1
                    ]);
                } else {
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
                } else {
                    Db::name('checkcodeLimit')->where('type', $type)->where('key', $sendto)
                        ->setInc('count', 1);
                }
            }
            return true;
        }

        return false;
    }

    public function sendMobileCode($mobile, $timeLimit, $tpltype)
    {
        if (!ValidateHelper::isMobile($mobile)) {
            $this->setError('手机号码格式错误');
            return false;
        }
        $exist = Db::name('Checkcode')->where('type', 0)
            ->where('sendto', $mobile)
            ->where('is_check', 0)->find();
        $newcode = random_str(6, 'number');
        if (empty($exist)) {
            $data = [
                'type' => 0,
                'sendto' => $mobile,
                'code' => $newcode,
                'create_time' => time(),
                'is_check' => 0
            ];
            Db::name('Checkcode')->insert($data);
        } else {

            //验证发送时间间隔
            if (time() - $exist['create_time'] < $timeLimit) {
                $this->setError('验证码发送过于频繁');
                return false;
            }

            Db::name('Checkcode')->where('id', $exist['id'])
                ->update(['code' => $newcode, 'create_time' => time()]);
        }

        $config = getSettings();
        if (isset($config['aliyun_dysms_' . $tpltype])) {
            $tplCode = $config['aliyun_dysms_' . $tpltype];
        }
        if (empty($tplCode) && $tpltype != 'verify') {
            $tplCode = $config['aliyun_dysms_verify'];
        }
        if (empty($tplCode)) {
            $this->setError('模板消息未设置');
            return false;
        }

        $aliyun = new Aliyun($config);
        $sended = $aliyun->sendSms($mobile, $newcode, $tplCode, $config['aliyun_dysms_sign']);
        if (!$sended) {
            $this->setError($aliyun->get_error_msg());
        }


        /* @session_write_close();
        $content="您本次验证码为{$newcode}仅用于会员注册。请在10分钟内使用！";
        $sms=new UmsHttp(getSettings());
        $sended=$sms->send($mobile,$content); */
        return $sended;
    }

    public function sendEmailCode($email, $timeLimit)
    {
        if (!ValidateHelper::isEmail($email)) {
            $this->setError('邮箱地址格式错误');
            return false;
        }
        $exist = Db::name('Checkcode')->where('type', 1)
            ->where('sendto', $email)
            ->where('is_check', 0)->find();
        $newcode = random_str(8, 'string');
        if (empty($exist)) {
            $data = [
                'type' => 1,
                'sendto' => $email,
                'code' => $newcode,
                'create_time' => time(),
                'is_check' => 0
            ];
            Db::name('Checkcode')->insert($data);
        } else {

            //验证发送时间间隔
            if (time() - $exist['create_time'] < $timeLimit) {
                $this->setError('验证码发送过于频繁');
                return false;
            }

            Db::name('Checkcode')->where('type', 1)->where('sendto', $email)
                ->update(['code' => $newcode, 'create_time' => time()]);
        }
        @session_write_close();
        $content = "您本次验证码为{$newcode}仅用于会员注册。请在10分钟内使用！";
        $settings = getSettings();
        $this->_setEmailConfig($settings);
        $sended = $this->_sendEmail($email, $settings['site-webname'] . '-验证码', $content);
        return $sended;
    }

    public function verifyCode($sendto, $code)
    {
        $crow = Db::name('checkcode')
            ->where('sendto', $sendto)
            ->where('code', $code)
            ->where('is_check', 0)
            ->order('create_time DESC')->find();
        $time = time();
        if (!empty($crow) && $crow['create_time'] > $time - 60 * 5) {
            Db::name('checkcode')->where('id', $crow['id'])->update(array('is_check' => 1, 'check_time' => $time));
            return true;
        }
        return false;
    }
}
