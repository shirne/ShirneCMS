<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/4/15
 * Time: 23:37
 */

namespace sdk;


class AccountBase
{
    protected $token;
    protected $encodingAesKey;
    protected $encrypt_type;
    protected $appid;
    protected $appsecret;
    protected $access_token;

    protected $debug;
    public $errCode = 40001;
    protected $errCodeKey='errcode';
    public $errMsg = "no access";
    protected $errMsgKey='errmsg';
    protected $logcallback;

    public function __construct($options)
    {
        $this->token = isset($options['token'])?$options['token']:'';
        $this->encodingAesKey = isset($options['encodingaeskey'])?$options['encodingaeskey']:'';
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

    protected function returnResult($result){
        if(is_array($result)){
            if($result[$this->errCodeKey]){
                $this->errCode=$result[$this->errCodeKey];
                $this->errMsg=$result[$this->errMsgKey];
                return false;
            }
            return $result;
        }
        $debug=debug_backtrace(0,2);
        $this->errCode=-1;
        if(!empty($debug)) {
            $this->errMsg = $debug[1]['class'] . $debug[1]['type'] . $debug[1]['function'] . ' at ' . $debug[1]['file'] . '(line ' . $debug[1]['line'] . ')';
        }else{
            throw new \Exception('未知错误');
        }
        return false;
    }

    /**
     * 生成随机字串
     * @param int $length 长度，默认为16，最长为32字节
     * @return string
     */
    public function generateNonceStr($length=16){
        // 密码字符集，可任意添加你需要的字符
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for($i = 0; $i < $length; $i++)
        {
            $str .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $str;
    }

    public static function json_encode($arr) {
        return json_encode($arr,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }


    /**
     * 兼容式请求
     * @param $url
     * @param $param
     * @param string $data
     * @param string $method
     * @return bool|string
     */
    protected function http($url, $param, $data = '', $method = 'GET'){
        if(!empty($param)){
            $url .= (strpos($url,'?')===false?'?':'&') . http_build_query($param);
        }
        if($method=='GET'){
            return $this->http_get($url);
        }else{
            return $this->http_post($url,$data);
        }
    }

    /**
     * GET 请求
     * @param string $url
     * @return string|bool
     */
    protected function http_get($url){
        $oCurl = curl_init();
        if(stripos($url,"https://")!==FALSE){
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if(intval($aStatus["http_code"])==200){
            return $sContent;
        }else{
            return false;
        }
    }

    /**
     * POST 请求
     * @param $url string
     * @param $param array|string
     * @param $post_file boolean 是否文件上传
     * @return string content
     */
    protected function http_post($url,$param,$post_file=false){
        $oCurl = curl_init();
        if(stripos($url,"https://")!==FALSE){
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }
        if (is_string($param) || $post_file) {
            $strPOST = $param;
        } else {
            $aPOST = array();
            foreach($param as $key=>$val){
                $aPOST[] = $key."=".urlencode($val);
            }
            $strPOST =  join("&", $aPOST);
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($oCurl, CURLOPT_POST,true);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS,$strPOST);
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if(intval($aStatus["http_code"])==200){
            return $sContent;
        }else{
            return false;
        }
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