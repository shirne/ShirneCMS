<?php


namespace shirne\third;

/**
 * 聚合API接口
 * Class JuheAPI
 * @package shirne\third
 */
class JuheAPI extends ThirdBase
{
    public function __construct($appkey='')
    {
        parent::__construct([
            'appsecret'=>$appkey
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
    public function query($name, $params, $key='', $type = 'apis', $ispost=0)
    {
        $this->clear_error();
        $name = preg_replace('/[^\w\d\/\.]/','',$name);
        $type = preg_replace('/[^\w\d\/\.]/','',$type);
        if(empty($key)){
            if(empty($this->appsecret[$name])) {
                $key = $this->appsecret[$name];
            }
        }
        if(empty($key)){
            $this->set_error('接口['.$name.'] APP Key配置不正确');
            return false;
        }
        $url = "http://{$type}.juhe.cn/{$name}";
        $params['key']=$key;
        $content = $this->juhecurl($url,$params,$ispost);
    
        if($content){
            $result = @json_decode($content,true);
            if($result) {
                $error_code = $result['error_code'];
                $this->set_error($result['reason'], $error_code);
                if ($error_code == 0) {
                    return $result['result'];
                }
            }else{
                $this->set_error('请求内容解析失败');
            }
        }else{
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
    public function sms_send($mobile,$params, $tplid){
        $this->clear_error();
        
        list(,$method)=explode('::',__METHOD__);
        if(empty($this->appsecret[$method])){
            $this->set_error('APP Key配置不正确');
            return false;
        }
        
        $sendUrl = 'http://v.juhe.cn/sms/send';
        $content=[];
        foreach ($params as $k=>$val){
            $content[]= "#$k#=$val";
        }
        $smsConf = array(
            'key'   => $this->appsecret[$method],
            'mobile'    => $mobile,
            'tpl_id'    => $tplid,
            'tpl_value' =>implode('&',$content)
        );
        
        $content = $this->juhecurl($sendUrl,$smsConf,1);
        
        if($content){
            $result = json_decode($content,true);
            if($result) {
                $error_code = $result['error_code'];
                if ($error_code == 0) {
                    // 短信ID $result['result']['sid']
                    return $result['result'];
                } else {
                    $this->set_error($result['reason'], $error_code);
                }
            }else{
                $this->set_error('请求内容解析失败');
            }
        }else{
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
    public function enterprise_ent3($regid, $comname, $pername){
        $this->clear_error();
        
        list(,$method)=explode('::',__METHOD__);
        if(empty($this->appsecret[$method])){
            $this->set_error('APP Key配置不正确');
            return false;
        }
        
        //先进行格式验证
        if(!$this->is_registration_no($regid)){
            $this->set_error('注册号/统一社会信用代码格式错误');
            return false;
        }
        if(!$this->is_truename($pername)){
            $this->set_error('法人姓名必须为2-4个中文汉字');
            return false;
        }
        if(!$this->is_idcard($regid)){
            $this->set_error('身份证号码格式错误');
            return false;
        }
        
        $url = "http://op.juhe.cn/enterprise/ent3";
        $params = array(
            "keyword" => $regid,
            "name" => $comname,
            'oper_name'=>$pername,
            "key" => $this->appsecret[$method],
        );
        $paramstring = http_build_query($params);
        $content = $this->juhecurl($url,$paramstring);
        $result = json_decode($content,true);
        if($result){
            if($result['error_code']=='0'){
                if($result['result']['status'] == '1') {
                    return true;
                }elseif($result['result']['status'] == '12'){
                    $this->set_error('企业法人不匹配',3);
                }elseif($result['result']['status'] == '13'){
                    $this->set_error('公司名称不匹配',2);
                }else{
                    $this->set_error('公司与企业法人不匹配',1);
                }
            }else{
                $this->set_error($result['reson'],$result['error_code']);
            }
        }else{
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
    public function idcard($idcard, $realname){
        $this->clear_error();
        
        list(,$method)=explode('::',__METHOD__);
        if(empty($this->appsecret[$method])){
            $this->set_error('APP Key配置不正确');
            return false;
        }
        
        //先进行格式验证
        if(!$this->is_truename($realname)){
            $this->set_error('姓名必须为2-4个中文汉字');
            return false;
        }
        if(!$this->is_idcard($idcard)){
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
        $content = $this->juhecurl($url,$paramstring);
        
        $result = json_decode($content,true);
        if($result){
            if($result['error_code']=='0'){
                if($result['result']['res'] == '1'){
                    return true;
                }else{
                    $this->set_error('身份证号码和真实姓名不一致',1);
                }
            }else{
                $this->set_error($result['reson'],$result['error_code']);
            }
        }else{
            $this->set_error('请求失败');
        }
        return false;
    }
    
    
    public function is_registration_no($regid){
        $regx = '/(^(?:(?![IOZSV])[\dA-Z]){2}\d{6}(?:(?![IOZSV])[\dA-Z]){10}$)|(^\d{15}$)/';
        if(!preg_match($regx, $regid))
        {
            return false;
        }
        return false;
    }
    
    public function is_truename($name){
        if (preg_match('/^([\xe4-\xe9][\x80-\xbf]{2}){2,4}$/', $name)) {
            return true;
        }
        return false;
    }
    
    public function is_idcard($idcard){
        $regx = "/^\d{17}[0-9X]$/";
        if(!preg_match($regx, $idcard))
        {
            return false;
        }
        $regx = "/^(\d{6})+(\d{4})+(\d{2})+(\d{2})+(\d{3})([0-9]|X)$/";
        @preg_match($regx, $idcard, $arr_split);
        $dtm_birth = $arr_split[2] . '/' . $arr_split[3]. '/' .$arr_split[4];
        if(!strtotime($dtm_birth)) //检查生日日期是否正确
        {
            return false;
        }
        else
        {
            //检验18位身份证的校验码是否正确。
            //校验位按照ISO 7064:1983.MOD 11-2的规定生成，X可以认为是数字10。
            $arr_int = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
            $arr_ch = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
            $sign = 0;
            for ( $i = 0; $i < 17; $i++ )
            {
                $b = (int) $idcard{$i};
                $w = $arr_int[$i];
                $sign += $b * $w;
            }
            $n = $sign % 11;
            $val_num = $arr_ch[$n];
            if ($val_num != substr($idcard,17, 1))
            {
                return false;
            }
            else
            {
                return true;
            }
        }
    }
    
    /**
     * 请求接口返回内容
     * @param  string $url [请求的URL地址]
     * @param  string|bool $params [请求的参数]
     * @param  int|bool $ispost [是否采用POST形式]
     * @return  string
     */
    protected function juhecurl($url,$params=false,$ispost=0){
        return $this->http($url,$params,$ispost);
    }
}