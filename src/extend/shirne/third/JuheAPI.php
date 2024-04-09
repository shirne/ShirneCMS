<?php


namespace shirne\third;

use shirne\common\ValidateHelper;

/**
 * 聚合API接口
 * Class JuheAPI
 * @package shirne\third
 */
class JuheAPI extends ThirdBase
{
    public function __construct($appkey = '')
    {
        parent::__construct([
            'appsecret' => $appkey
        ]);
        $this->userAgent = 'JuheData';
    }

    /**
     * 统一请求api
     * @param string $name 接口路径
     * @param array|string|null $params
     * @param string $key
     * @param string $type 接口域名第一段 ['apis','v','japi']
     * @param int $ispost
     * @return bool
     */
    public function query($name, $params, $key = '', $type = 'apis', $ispost = 0)
    {
        $this->clear_error();
        $name = preg_replace('/[^\w\d\/\.]/', '', $name);
        $type = preg_replace('/[^\w\d\/\.]/', '', $type);
        if (empty($key)) {
            if (empty($this->appsecret[$name])) {
                $key = $this->appsecret[$name];
            }
        }
        if (empty($key)) {
            $this->set_error('接口[' . $name . '] APP Key配置不正确');
            return false;
        }
        $url = "http://{$type}.juhe.cn/{$name}";
        $params['key'] = $key;
        $content = $this->juhecurl($url, $params, $ispost);

        if ($content) {
            $result = @json_decode($content, true);
            if ($result) {
                $error_code = $result['error_code'];
                $this->set_error($result['reason'], $error_code);
                if ($error_code == 0) {
                    return $result['result'];
                }
            } else {
                $this->set_error('请求内容解析失败');
            }
        } else {
            $this->set_error("接口[{$name}]请求失败");
        }
        return false;
    }

    /**
     * 短信API服务
     * @param $mobile
     * @param $params
     * @param $tplid
     * @return bool
     */
    public function sms_send($mobile, $params, $tplid)
    {
        $this->clear_error();

        list(, $method) = explode('::', __METHOD__);
        if (empty($this->appsecret[$method])) {
            $this->set_error('APP Key配置不正确');
            return false;
        }

        $sendUrl = 'http://v.juhe.cn/sms/send';
        $content = [];
        foreach ($params as $k => $val) {
            $content[] = "#$k#=$val";
        }
        $smsConf = array(
            'key'   => $this->appsecret[$method],
            'mobile'    => $mobile,
            'tpl_id'    => $tplid,
            'tpl_value' => implode('&', $content)
        );

        $content = $this->juhecurl($sendUrl, $smsConf, 1);

        if ($content) {
            $result = json_decode($content, true);
            if ($result) {
                $error_code = $result['error_code'];
                if ($error_code == 0) {
                    // 短信ID $result['result']['sid']
                    return $result['result'];
                } else {
                    $this->set_error($result['reason'], $error_code);
                }
            } else {
                $this->set_error('请求内容解析失败');
            }
        } else {
            $this->set_error('请求发送短信失败');
        }
        return false;
    }

    /**
     * 企业三要素核验
     * @param $regid
     * @param $comname
     * @param $pername
     * @return bool
     */
    public function enterprise_ent3($regid, $comname, $pername)
    {
        $this->clear_error();

        list(, $method) = explode('::', __METHOD__);
        if (empty($this->appsecret[$method])) {
            $this->set_error('APP Key配置不正确');
            return false;
        }

        //先进行格式验证
        if (!ValidateHelper::isRegistrationNO($regid)) {
            $this->set_error('注册号/统一社会信用代码格式错误');
            return false;
        }
        if (!ValidateHelper::isRealname($pername)) {
            $this->set_error('法人姓名必须为2-4个中文汉字');
            return false;
        }
        if (!ValidateHelper::isIdcard($regid)) {
            $this->set_error('身份证号码格式错误');
            return false;
        }

        $url = "http://op.juhe.cn/enterprise/ent3";
        $params = array(
            "keyword" => $regid,
            "name" => $comname,
            'oper_name' => $pername,
            "key" => $this->appsecret[$method],
        );
        $paramstring = http_build_query($params);
        $content = $this->juhecurl($url, $paramstring);
        $result = json_decode($content, true);
        if ($result) {
            if ($result['error_code'] == '0') {
                if ($result['result']['status'] == '1') {
                    return true;
                } elseif ($result['result']['status'] == '12') {
                    $this->set_error('企业法人不匹配', 3);
                } elseif ($result['result']['status'] == '13') {
                    $this->set_error('公司名称不匹配', 2);
                } else {
                    $this->set_error('公司与企业法人不匹配', 1);
                }
            } else {
                $this->set_error($result['reson'], $result['error_code']);
            }
        } else {
            $this->set_error('请求失败');
        }
        return false;
    }

    /**
     * 身份证实名认证
     * @param $idcard
     * @param $realname
     * @return bool
     */
    public function idcard($idcard, $realname)
    {
        $this->clear_error();

        list(, $method) = explode('::', __METHOD__);
        if (empty($this->appsecret[$method])) {
            $this->set_error('APP Key配置不正确');
            return false;
        }

        //先进行格式验证
        if (!ValidateHelper::isRealname($realname)) {
            $this->set_error('姓名必须为2-4个中文汉字');
            return false;
        }
        if (!ValidateHelper::isIdcard($idcard)) {
            $this->set_error('身份证号码格式错误');
            return false;
        }

        $url = "http://op.juhe.cn/idcard/query";
        $params = array(
            "idcard" => $idcard,
            "realname" => $realname,
            "key" => $this->appsecret[$method],
        );
        $paramstring = http_build_query($params);
        $content = $this->juhecurl($url, $paramstring);

        $result = json_decode($content, true);
        if ($result) {
            if ($result['error_code'] == '0') {
                if ($result['result']['res'] == '1') {
                    return true;
                } else {
                    $this->set_error('身份证号码和真实姓名不一致', 1);
                }
            } else {
                $this->set_error($result['reson'], $result['error_code']);
            }
        } else {
            $this->set_error('请求失败');
        }
        return false;
    }

    /**
     * 请求接口返回内容
     * @param  string $url [请求的URL地址]
     * @param  string|bool $params [请求的参数]
     * @param  int|bool $ispost [是否采用POST形式]
     * @return  string
     */
    protected function juhecurl($url, $params = false, $ispost = 0)
    {
        return $this->http($url, $params, $ispost);
    }
}
