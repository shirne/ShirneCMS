<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/9/30
 * Time: 17:10
 */

namespace third;


class UmsHttp extends ThirdBase
{
    private $_apiUrl = 'http://api.ums86.com:8899/sms/Api/Send.do'; // 发送短信接口地址

    public $SpCode;
    public $LoginName;
    public $Password;

    public function __construct($options)
    {
        parent::__construct($options);
        if(!empty($options['sms_spcode'])){
            $this->SpCode=$options['sms_spcode'];
        }
        if(!empty($options['sms_loginname'])){
            $this->LoginName=$options['sms_loginname'];
        }
        if(!empty($options['sms_password'])){
            $this->Password=$options['sms_password'];
        }
    }

    public function send($mobile,$content) {
        if(empty($this->SpCode)||empty($this->LoginName) || empty($this->Password)){
            $this->errMsg='短信接口配置错误';
            return false;
        }
        $params = array(
            "SpCode" => $this->SpCode,
            "LoginName" => $this->LoginName,
            "Password" => $this->Password,
            "MessageContent" => iconv("UTF-8", "GB2312//IGNORE", $content),
            "UserNumber" => $mobile,
            "SerialNumber" => '',
            "ScheduleTime" => '',
            "ExtendAccessNum" => '',
            "f" => '',
        );
        //$data = http_build_query($params);
        $res = iconv('GB2312', 'UTF-8//IGNORE', $this->http_post($this->_apiUrl,$params));
        $resArr = array();
        parse_str($res, $resArr);

        if (!empty($resArr) && $resArr["result"] == 0) return true;
        else {
            if (empty($this->errMsg)) $this->errMsg = isset($resArr["description"]) ? $resArr["description"] : '未知错误';
            return false;
        }
    }
}