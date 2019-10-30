<?php

namespace shirne\third;

use think\facade\Log;

/**
 * 第三方接口基类
 * Class ThirdBase
 * @package third
 */
class ThirdBase extends ThirdModelBase
{
    protected $appid;
    protected $appsecret;

    protected $baseURL;
    
    
    protected $userAgent;

    protected $logcallback;

    public function __construct($options)
    {
        $this->userAgent = 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36';
        $this->appid = isset($options['appid'])?$options['appid']:'';
        $this->appsecret = isset($options['appsecret'])?$options['appsecret']:'';
        $this->logcallback = isset($options['logcallback'])?$options['logcallback']:false;
    }
    

    /**
     * 日志记录，可被重载。
     * @param mixed $log 输入日志
     * @return mixed
     */
    protected function log($log){
        if ($this->debug && $this->logcallback!==false) {
            if (is_array($log)) $log = print_r($log,true);
            return call_user_func($this->logcallback,$log);
        }
        return false;
    }

    /**
     * http请求
     * @param $url
     * @param string $data
     * @param string $method
     * @return bool|string
     */
    protected function http($url, $data = '', $method = 'GET'){
        $oCurl = curl_init();
        if(stripos($url,"https://")!==FALSE){
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
            //curl_setopt($oCurl, CURLOPT_SSLVERSION, 1);
        }
        curl_setopt($oCurl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($oCurl, CURLOPT_USERAGENT, $this->userAgent);
        curl_setopt($oCurl, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($oCurl, CURLOPT_TIMEOUT, 60);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($oCurl, CURLOPT_FOLLOWLOCATION, 1);
        if(strtoupper($method)=='POST') {
            curl_setopt($oCurl, CURLOPT_URL, $url);
            curl_setopt($oCurl, CURLOPT_POST, 1);
            if(!empty($data))curl_setopt($oCurl, CURLOPT_POSTFIELDS, $data);
        }else{
            if(is_array($data)) {
                $strQuery =  http_build_query( $data);
            }
            if(!empty($strQuery)){
                $url .= (strpos($url,'?')!==false?'&':'?').$strQuery;
            }
            curl_setopt($oCurl, CURLOPT_URL, $url);
        }
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        Log::write($url.(empty($data)?'':("\n".var_export($data,true)))."\n".var_export($sContent,TRUE),'HTTP');
        if(intval($aStatus["http_code"])==200){
            return $sContent;
        }else{
            $this->set_error('HTTP请求错误',-2);
            return false;
        }
    }

    /**
     * GET 请求
     * @param string $url
     * @param string|array $param
     * @return string|bool
     */
    protected function http_get($url,$param=''){
        return $this->http($url,$param);
    }

    /**
     * POST 请求
     * @param $url string
     * @param $param array|string
     * @param $post_type int 发送编码方式(1 强制urlencoded)
     * @return string content
     */
    protected function http_post($url,$param='',$post_type=0){
        if($post_type==1 && is_array($param)){
            $param=http_build_query($param);
        }
        return $this->http($url,$param,'POST');
    }

}