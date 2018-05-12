<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirn
 * Date: 2018/5/12
 * Time: 18:06
 */

namespace sdk;


class AESEnctypt
{
    public $key;
    private $len;

    /**
     * @var string 为什么不是 AES-128-CBC?  我也不知道
     */
    private $method='AES-256-CBC';

    /**
     * AESEnctypt constructor.
     * @param $k
     */
    function __construct($k) {
        $this->key = base64_decode($k . "=");
        $this->len=openssl_cipher_iv_length($this->method);
    }

    /**
     * 对明文进行加密
     * @param string $text 需要加密的明文
     * @param string $appid
     * @return bool|string 加密后的密文
     */
    public function encrypt($text, $appid)
    {

        $random = $this->getRandomStr();
        $text = $random . pack("N", strlen($text)) . $text . $appid;
        $iv = substr($this->key, 0, $this->len);

        //php7.1才支持内置pkcs7 这里手动处理
        $pkc_encoder = new PKCS7Encoder;
        $text = $pkc_encoder->encode($text);

        $result = openssl_encrypt($text,$this->method,$this->key,OPENSSL_ZERO_PADDING   ,$iv);
        if($result){
            return $result;
        }
        return false;
    }

    /**
     * 对密文进行解密
     * @param string $encrypted 需要解密的密文
     * @param string $appid
     * @return string|array 解密得到的明文
     */
    public function decrypt($encrypted, $appid)
    {

        //使用BASE64对需要解密的字符串进行解码
        //$ciphertext_dec = base64_decode($encrypted);
        $iv = substr($this->key, 0, $this->len);

        $result=openssl_decrypt($encrypted,$this->method,$this->key,OPENSSL_ZERO_PADDING  ,$iv);
        if(!$result || strlen($result) < 16){
            return false;
        }
        $pkc_encoder = new PKCS7Encoder;
        $result = $pkc_encoder->decode($result);

        $content = substr($result, 16, strlen($result));
        $len_list = unpack("N", substr($content, 0, 4));
        $xml_len = $len_list[1];
        $xml_content = substr($content, 4, $xml_len);
        $from_appid = substr($content, $xml_len + 4);

        if ($from_appid != $appid) {
            return false;
        }

        return $xml_content;

    }


    /**
     * 随机生成16位字符串
     * @param $len int 长度
     * @return string 生成的字符串
     */
    function getRandomStr($len=16)
    {

        $str = "";
        $str_pol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($str_pol) - 1;
        for ($i = 0; $i < $len; $i++) {
            $str .= $str_pol[mt_rand(0, $max)];
        }
        return $str;
    }
}