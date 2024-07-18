<?php

namespace shirne\common;

class ValidateHelper{
    
    public static function isRegistrationNO($regid)
    {
        if(strlen($regid) == 18){
            return self::check_registration_no($regid);
        }elseif(strlen($regid) == 15){
            return self::check_business_code($regid);
        }
        
        return false;
    }
    
    private static function check_business_code($code){
        if (empty($code)){
            return  false;
        }else if(strlen($code)!=15){
            return false;
        }
        if(!preg_match('/^[A-Za-z0-9]\w{14}$/',$code)){
            return false;
        }
        $businesslicensePrex14 = substr($code,0,14);
        $businesslicense15 = substr($code,14,1);
        $ints = [];
        for($i=0; $i<14;$i++){
            $ints[$i] = intval($businesslicensePrex14[$i]);
        }
        $checkcode=self::getCheckCode($ints);
        if($checkcode == $businesslicense15){
            return  true;
        }
        return false;
    }
    /**
     * 获取 营业执照注册号的校验码
     * @param $ints
     * @return int
     */
    private static function getCheckCode($ints)
    {
        if (!empty($ints)) {
            $cj = 0;
            for ($i = 0; $i < count($ints); $i++) {
                $ti = $ints[$i];
                $pj = ($cj % 11) == 0 ? 10 : ($cj % 11);
                $si = $pj + $ti;
                $cj = (0 == $si % 10 ? 10 : $si % 10) * 2;
                if ($i == count($ints) - 1) {
                    $pj = ($cj % 11) == 0 ? 10 : ($cj % 11);
                    
                    return $pj == 1 ? 1 : 11 - $pj;
                }
            }
        }
        
        return -1;
    }
    private static function check_registration_no($code)
    {
        if (strlen($code) != 18) {
            return false;
        }
        //$reg = '/(^(?:(?![IOZSV])[\dA-Z]){2}\d{6}(?:(?![IOZSV])[\dA-Z]){10}$)|(^\d{15}$)/';
        //$reg = '/^[1-9A-GY]{1}[1239]{1}[1-5]{1}[0-9]{5}[0-9A-Z]{10}$/';
        //$reg = '/^[^_IOZSVa-z\W]{2}\d{6}[^_IOZSVa-z\W]{10}$/';
        $reg = '/^([0-9ABCDEFGHJKLMNPQRTUWXY]{2})([0-9]{6})([0-9ABCDEFGHJKLMNPQRTUWXY]{10})$/';
        if (!preg_match($reg,$code)) {
            return false;
        }
        
        $str = '0123456789ABCDEFGHJKLMNPQRTUWXY';
        $ws = [1, 3, 9, 27, 19, 26, 16, 17, 20, 29, 25, 13, 8, 24, 10, 30, 28];
        $codes = array();
        $codes[0] = substr($code, 0, strlen($code) - 1);
        $codes[1] = substr($code, strlen($code) - 1, 1);
        $sum = 0;
        for ($i = 0; $i < 17; $i++) {
            $sum += strpos($str, $codes[0][$i]) * $ws[$i];
        }
        $c18 = $str[31 - ($sum % 31)];
        
        if ($c18 != $codes[1]) {
            return false;
        }
        
        return true;
    }
    
    public static function isRealname($name)
    {
        if (preg_match('/^([\xe4-\xe9][\x80-\xbf]{2}){2,4}$/', $name)) {
            return true;
        }
        
        return false;
    }

    public static function isEmail($email){
        if (preg_match('/^[\w]+@[\w]+(\.[\w]+)+$/', $email)) {
            return true;
        }
        return false;
    }
    
    public static function isMobile($mobile){
        if (preg_match('/^1[3-9][0-9]{9}$/', $mobile)) {
            return true;
        }
        return false;
    }
    
    public static function isBankcard($bankcard){
        $checkcode=self::get_bankcard_checkcode(substr($bankcard,0,strlen($bankcard)-1));
        if($checkcode == 'N'){
            return false;
        }
        $code = substr($bankcard,-1);
        if ($checkcode == $code) {
            return true;
        }
        return false;
    }
    
    private static function get_bankcard_checkcode($nocode_cardid) {
        if (empty($nocode_cardid)
            || !preg_match('/^\\d+$/',$nocode_cardid)) {
            // 如果传的不是数据返回N
            return 'N';
        }
        $chars = str_split($nocode_cardid);
        $luhmSum = 0;
        for ($i = count($chars) - 1, $j = 0; $i >= 0; $i--, $j++) {
            $k = intval($chars[$i]);
            if ($j % 2 == 0) {
                $k *= 2;
                $k = $k / 10 + $k % 10;
            }
            $luhmSum += $k;
        }
        return ($luhmSum % 10 == 0) ? '0' : strval(10 - $luhmSum % 10);
    }
    
    public static function isIdcard($idcard)
    {
        $regx = "/^\d{17}[0-9X]$/";
        if (!preg_match($regx, $idcard)) {
            return false;
        }
        $regx = "/^(\d{6})+(\d{4})+(\d{2})+(\d{2})+(\d{3})([0-9]|X)$/";
        @preg_match($regx, $idcard, $arr_split);
        $dtm_birth = $arr_split[2] . '/' . $arr_split[3] . '/' . $arr_split[4];
        if (!strtotime($dtm_birth)) //检查生日日期是否正确
        {
            return false;
        } else {
            //检验18位身份证的校验码是否正确。
            //校验位按照ISO 7064:1983.MOD 11-2的规定生成，X可以认为是数字10。
            $arr_int = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
            $arr_ch = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
            $sign = 0;
            for ($i = 0; $i < 17; $i++) {
                $b = (int)$idcard[$i];
                $w = $arr_int[$i];
                $sign += $b * $w;
            }
            $n = $sign % 11;
            $val_num = $arr_ch[$n];
            if ($val_num != substr($idcard, 17, 1)) {
                return false;
            } else {
                return true;
            }
        }
    }
}