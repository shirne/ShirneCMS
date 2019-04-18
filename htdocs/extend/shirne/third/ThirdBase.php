<?php

namespace shirne\third;

/**
 * 第三方接口基类
 * Class ThirdBase
 * @package third
 */
class ThirdBase
{
    protected $appid;
    protected $appsecret;

    protected $baseURL;

    protected $debug;
    public $errCode = 40001;
    protected $errCodeKey='errcode';
    public $errMsg = "no access";
    protected $errMsgKey='errmsg';
    protected $logcallback;

    public function __construct($options)
    {
        $this->appid = isset($options['appid'])?$options['appid']:'';
        $this->appsecret = isset($options['appsecret'])?$options['appsecret']:'';
        $this->debug = isset($options['debug'])?$options['debug']:false;
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
        curl_setopt($oCurl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36');
        curl_setopt($oCurl, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($oCurl, CURLOPT_TIMEOUT, 60);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
        if(strtoupper($method)=='POST') {
            curl_setopt($oCurl, CURLOPT_URL, $url);
            curl_setopt($oCurl, CURLOPT_POST, true);
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

    /**
     * 设置缓存，按需重载
     * @param string $cachename
     * @param mixed $value
     * @param int $expired
     * @return boolean
     */
    protected function setCache($cachename,$value,$expired){
        cache($cachename,$value,$expired);
        return false;
    }

    /**
     * 获取缓存，按需重载
     * @param string $cachename
     * @return mixed
     */
    protected function getCache($cachename){
        cache($cachename);
        return false;
    }

    /**
     * 清除缓存，按需重载
     * @param string $cachename
     * @return boolean
     */
    protected function removeCache($cachename){
        cache($cachename,null);
        return false;
    }
}