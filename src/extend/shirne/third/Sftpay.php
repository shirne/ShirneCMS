<?php
namespace shirne\third;

/**
 * RSA最大加密明文大小
 */
define('MAX_ENCRYPT_BLOCK', 117);

/**
 * RSA最大解密密文大小
 */
define('MAX_DECRYPT_BLOCK', 128);

class Sftpay extends ThirdBase
{

    protected $thirdSysId;
    protected $md5Key;
    protected $desKey;

    public function __construct($options)
    {
        parent::__construct($options);

        //测试环境
        /**
         * 124.127.94.39 emall.ccb.com
            124.127.94.39 login.ccb.com
            124.127.94.39 epay.ccb.com
            124.127.94.39 img.mall.ccb.com

         */
        //$this->baseURL = 'http://emall.ccb.com:8880/ecp/thirdPartAPI';

        //正式环境
        $this->baseURL = 'https://mall.ccb.com/ecp/thirdPartAPI';

        $this->thirdSysId = isset($options['thirdsysid'])?$options['thirdsysid']:'';
        $this->md5Key = isset($options['md5key'])?$options['md5key']:'';
        $this->desKey = isset($options['deskey'])?$options['deskey']:'';
    }

    protected function fixTransid($data){
        if(empty($data['TransID'])){
            $nos = explode('.',microtime(true));
            $data['TransID'] = $this->thirdSysId.date('YmdHis').substr('00000'.$nos[1], -5, 5);
        }elseif(strpos($data['TransID'], $this->thirdSysId) !== 0){
            $data['TransID'] = $this->thirdSysId.$data['TransID'];
        }
        return $data;
    }

    public function send($data){
        $data = $this->fixTransid($data);
        $code  = $data['TxCode'];
        $this->log($data);
        $enStr = $this->data_encrypt($data,$this->desKey);
        $auth = $this->md5_encrypt($this->thirdSysId.$code.$enStr.$this->md5Key);

        $this->log([$enStr,$auth]);
        echo <<<FORM
        <form action="{$this->baseURL}" name="sftpayform" method="POST" >
        <input type="hidden" name="ThirdSysID" value="{$this->thirdSysId}"/>
        <input type="hidden" name="TxCode" value="{$code}"/>
        <input type="hidden" name="Data" value="{$enStr}"/>
        <input type="hidden" name="Auth" value="{$auth}"/>
        </form>
        <script>document.forms.sftpayform.submit()</script>
FORM;
        exit;
    }

    public function request($data){
        $data = $this->fixTransid($data);
        $code  = $data['TxCode'];
        $enStr = $this->data_encrypt($data,$this->desKey);
        $auth = $this->md5_encrypt($this->thirdSysId.$code.$enStr.$this->md5Key);

        $result = $this->http_post($this->baseURL, [
            'ThirdSysID'=>$this->thirdSysId,
            'TxCode'=>$code,
            'Data'=>$enStr,
            'Auth'=>$auth
        ], 1);
        if($resultData = @json_decode($result, true)){
            return $resultData;
        }
        return $result;
    }

    public function verify($data){
        $ThirdSysID = $data['ThirdSysID'];
        $TxCode = $data['TxCode'];
        $Data = $data["Data"] ;	
        $Auth = $data["Auth"] ;

        $myauth = $this->md5_encrypt($ThirdSysID.$TxCode.$Data.$this->md5Key);
        
        if($Auth==$myauth)
        {
            return true;
        }else{
            return false;
        }
    }

    public function decode($data){
        $decryptData = $this->data_decrypt($data,$this->desKey);

        return $decryptData;
    }

    protected function md5_encrypt($data){
        return md5($data);
    }   

    protected function data_encrypt($data){
        $datastr = json_encode($data,JSON_UNESCAPED_UNICODE);
        $strs = str_split($datastr, MAX_ENCRYPT_BLOCK);
        $encrypts='';
        foreach($strs as $str){
            $isok = openssl_public_encrypt($str,$encrypted,$this->desKey);
            if(!$isok){
                exit(openssl_error_string());
            }
            $encrypts.=$encrypted;
        }

        return urlencode(base64_encode($encrypts));
    }

    protected function data_decrypt($data){
        $datastr = empty($data)?'':base64_decode(urldecode($data));

        $strs = str_split($datastr, MAX_DECRYPT_BLOCK);
        $decrypts='';
        foreach($strs as $str){
            $isok = openssl_public_decrypt($str,$decrypted,$this->desKey);
            if(!$isok){
                exit(openssl_error_string());
            }
            $decrypts.=$decrypted;
        }

        return json_decode($decrypts, true);
    }

}