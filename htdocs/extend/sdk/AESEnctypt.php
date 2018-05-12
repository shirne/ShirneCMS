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
    private $method='AES-128-CBC';

    function __construct($k) {
        $this->key = base64_decode($k . "=");
    }
    public function encrypt($text, $appid)
    {

        try {
            $random = $this->getRandomStr();
            $text = $random . pack("N", strlen($text)) . $text . $appid;
            $iv = substr($this->key, 0, 16);

            return openssl_encrypt($text,$this->method,$this->key,OPENSSL_ZERO_PADDING,$iv);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 对密文进行解密
     * @param string $encrypted 需要解密的密文
     * @return string|array 解密得到的明文
     */
    public function decrypt($encrypted, $appid)
    {

        try {
            //使用BASE64对需要解密的字符串进行解码
            $ciphertext_dec = base64_decode($encrypted);
            $iv = substr($this->key, 0, 16);

            $result=openssl_decrypt($ciphertext_dec,$this->method,$this->key,OPENSSL_ZERO_PADDING,$iv);
        } catch (\Exception $e) {
            return false;
        }


        try {

            if (strlen($result) < 16)
                return false;
            $content = substr($result, 16, strlen($result));
            $len_list = unpack("N", substr($content, 0, 4));
            $xml_len = $len_list[1];
            $xml_content = substr($content, 4, $xml_len);
            $from_appid = substr($content, $xml_len + 4);
            if (!$appid)
                $appid = $from_appid;

        } catch (\Exception $e) {
            return false;
        }
        if ($from_appid != $appid)
            return false;

        return $xml_content;

    }


    /**
     * 随机生成16位字符串
     * @return string 生成的字符串
     */
    function getRandomStr()
    {

        $str = "";
        $str_pol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($str_pol) - 1;
        for ($i = 0; $i < 16; $i++) {
            $str .= $str_pol[mt_rand(0, $max)];
        }
        return $str;
    }
}