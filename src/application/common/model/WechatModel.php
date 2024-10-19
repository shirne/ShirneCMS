<?php

namespace app\common\model;

use app\common\core\BaseModel;
use EasyWeChat\Factory;
use EasyWeChat\Payment\Application;
use EasyWeChat\OfficialAccount\Application as OfficialAccountApplication;
use EasyWeChat\MiniProgram\Application as MiniProgramApplication;
use EasyWeChat\OpenPlatform\Application as OpenPlatformApplication;
use EasyWeChat\Work\Application as WorkApplication;
use EasyWeChat\OpenWork\Application as OpenWorkApplication;
use EasyWeChat\MicroMerchant\Application as MicroMerchantApplication;

/**
 * Class WechatModel
 * @package app\common\model
 */
class WechatModel extends BaseModel
{
    protected $autoWriteTimestamp = true;

    public function setCertPathAttr($val, $data)
    {
        return $this->check_secure_file($val, $data);
    }
    public function setKeyPathAttr($val, $data)
    {
        return $this->check_secure_file($val, $data);
    }
    protected function check_secure_file($val, $data)
    {
        if (!empty($val)) {
            if (strpos($val, '/../cert/') !== 0) {
                $truefile = DOC_ROOT . $val;
                if (file_exists($truefile)) {
                    $fileparts = pathinfo($truefile);
                    if (!empty($fileparts['basename'])) {
                        $newname = '/../cert/' . $data['appid'] . '/' . $fileparts['basename'];
                        $path = dirname(DOC_ROOT . $newname);
                        if (!is_dir($path)) mkdir($path, 0777, true);
                        rename($truefile, DOC_ROOT . $newname);

                        return $newname;
                    }
                }
                return '';
            }
        }
        return $val;
    }

    public static function to_config($data)
    {
        $options = [
            'response_type' => 'array',
            'log' => config('log.wechat'),
        ];
        if ($data['account_type'] == 'work') {
            $options['corp_id'] = $data['appid'];
            $options['secret'] = $data['appsecret'];

            $options['agent_id'] = '';
        } else if ($data['account_type'] == 'openwork') {
            $options['corp_id'] = $data['appid'];

            $options['suite_id'] = '';
            $options['suite_secret'] = $data['appsecret'];

            $options['token'] = $data['token'];
            $options['aes_key'] = $data['encodingaeskey'];

            $options['reg_template_id'] = '';
            $options['redirect_uri_install'] = '';
            $options['redirect_uri_single'] = '';
            $options['redirect_uri_oauth'] = '';
        } else if ($data['account_type'] == 'micromerchant') {
            $options['appid'] = $data['appid'];
            $options['key'] = '';
            $options['apiv3_key'] = '';
            $options['cert_path'] = '';
            $options['key_path'] = '';


            $options['serial_no'] = '';
            $options['certificate'] = '';
        } else {
            $options['app_id'] = $data['appid'];
            $options['secret'] = $data['appsecret'];
            $options['token'] = $data['token'];
            $options['aes_key'] = $data['encodingaeskey'];
        }
        return $options;
    }

    public function toConfig()
    {
        return self::to_config($this);
    }

    protected static $lastWechat = null;
    public static function getLastWechat()
    {
        return static::$lastWechat;
    }

    /**
     * 创建一个Application对象
     * @param string $wechat 
     * @param bool $ispay 
     * @param array $payset 
     * @return false|Application|OfficialAccountApplication|MiniProgramApplication|OpenPlatformApplication|WorkApplication|OpenWorkApplication|MicroMerchantApplication|null 
     */
    public static function createApp($wechat = 'default', $ispay = false, $payset = [])
    {
        if ($wechat === 'default') {
            $wechat = static::where('is_default', 1)->find();
        } elseif (is_numeric($wechat)) {
            $wechat = static::get($wechat);
        } elseif (is_string($wechat)) {
            $wechat = static::where(['appid' => $wechat])->find();
        }
        static::$lastWechat = $wechat;
        if (!empty($wechat)) {
            if ($ispay) {
                $config = WechatModel::to_pay_config($wechat, $payset['notify'] ?? '', $payset['use_cert'] ?? false);
                if (empty($config)) {
                    return false;
                }
                return Factory::payment($config);
            }
            $options = self::to_config($wechat);

            switch ($wechat['account_type']) {
                case 'wechat':
                case 'subscribe':
                case 'service':
                    return Factory::officialAccount($options);
                    break;
                case 'miniprogram':
                case 'minigame':
                    return Factory::miniProgram($options);
                    break;
                case 'platform':
                    return Factory::openPlatform($options);
                    break;
                case 'work':
                    return Factory::work($options);
                case 'openwork':
                    return Factory::openWork($options);
                case 'micromerchant':
                    return Factory::microMerchant($options);
                default:

                    break;
            }
        }
        return null;
    }

    public static function to_pay_config($data, $notify = '', $useCert = false)
    {

        // 必要配置
        $config = [
            'app_id'             => $data['appid'],
            'mch_id'             => $data['mch_id'],
            'key'                => $data['key'],
            'log'                => config('log.wechat'),
        ];

        // 如需使用敏感接口（如退款、发送红包等）需要配置 API 证书路径(登录商户平台下载 API 证书)
        if ($useCert) {
            if (empty($data['cert_path']) || empty($data['key_path'])) {
                return false;
            }
            $config['cert_path'] = realpath(DOC_ROOT . $data['cert_path']);
            $config['key_path']  = realpath(DOC_ROOT . $data['key_path']);
        }

        if ($notify) {
            $notify = str_replace('__HASH__', $data['hash'], $notify);
            $config['notify_url'] = $notify;
        }
        return $config;
    }

    public function toPayConfig()
    {
        return self::to_pay_config($this);
    }
}
