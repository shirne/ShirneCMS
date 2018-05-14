<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
error_reporting(E_ERROR | E_WARNING | E_PARSE);

function writelog($message,$type=\think\Log::INFO){
    if(config('app_debug')==true){
        \think\facade\Log::write($message,$type);
    }
}

function file_download($filename,$data){
    return \think\Response::create($data, '\\extcore\\FileDownload', 200, [], ['file_name'=>$filename]);
}
function getMemberTypes(){
    return [
        '普通会员',
        '内部员工'
    ];
}
function getOauthTypes(){
    return [
        'weixin'=>'微信登录',
        'baidu'=>'百度登录',
        'coding'=>'CODING',
        'csdn'=>'CSDN',
        'gitee'=>'Gitee',
        'github'=>'Github',
        'oschina'=>'OSChina',
        'qq'=>'QQ登录',
        'weibo'=>'新浪微博',
    ];
}

function getMemberLevels()
{
    static $levels;
    if (empty($levels)) {
        $levels = cache('levels');
        if (empty($levels)) {
            $levels=array();
            $model=new \app\admin\model\MemberLevelModel();
            $data =  $model->order('sort ASC,level_id ASC')->select();
            foreach ($data as $level){
                $levels[$level['level_id']]=$level;
            }
            cache('levels', $levels);
        }
    }
    return $levels;
}

function getLevelConfig($levels){
    $configs=array(
        'commission_layer'=>0
    );
    foreach ($levels as $level){
        foreach ($configs as $k=>$v){
            $configs[$k] = max($v,$level[$k]);
        }
    }
    return $configs;
}

function getDefaultLevel(){
    $levels=getMemberLevels();
    foreach ($levels as $level){
        if($level['is_default']){
            return $level['level_id'];
        }
    }
    return 0;
}

function filter_specchar($str){
    return preg_replace("/\/|\~|\!|\@|\#|\\$|\%|\^|\&|\*|\(|\)|\_|\+|\{|\}|\:|\<|\>|\?|\[|\]|\,|\.|\/|\;|\'|\`|\-|\=|\\\|\|/",'',$str);
}

function current_url($withqry=true){
    $query=$withqry?$_REQUEST['QUERY_STRING']:'';
    if(!empty($query))$query='?'.$query;
    return current_domain().$_SERVER['REQUEST_URI'].$query;
}

function current_domain(){
    return ($_SERVER['HTTPS']=="On"?"https":"http").'://'.$_SERVER['SERVER_NAME'];
}

function idArr($id){
    if(strpos($id,',')>0){
        $ids=explode(',',$id);
        return array_map(function($i){
            return intval($i);
        },$ids);
    }else{
        return [intval($id)];
    }
}

/**
 * 检测文件编码
 * @param string $file 文件路径
 * @return string|null 返回 编码名 或 null
 */
function detect_encoding($file) {
    $list = array('GB2312', 'GBK', 'UTF-8', 'UTF-16LE', 'UTF-16BE', 'ISO-8859-1');
    $str = file_get_contents($file);
    foreach ($list as $item) {
        $tmp = mb_convert_encoding($str, $item, $item);
        if (md5($tmp) == md5($str)) {
            return $item;
        }
    }
    return null;
}

/**
 * 自动解析编码读入文件
 * @param string $file 文件路径
 * @param string $charset 读取编码
 * @return string 返回读取内容
 */
function auto_read($file, $charset='UTF-8') {
    $list = array('GB2312', 'GBK', 'UTF-8', 'UTF-16LE', 'UTF-16BE', 'ISO-8859-1');
    $str = file_get_contents($file);
    foreach ($list as $item) {
        $tmp = mb_convert_encoding($str, $item, $item);
        if (md5($tmp) == md5($str)) {
            return mb_convert_encoding($str, $charset, $item);
        }
    }
    return "";
}

/**
 * 兼容的array_combine
 * @param $keys
 * @param $vals
 * @param bool $pad
 * @return array
 */
function array_combine_cmp($keys,$vals,$pad=FALSE){
    $kcount = count($keys);
    $vcount = count($vals);
    if (!$pad) {
        if($kcount > $vcount) {
            $keys = array_slice($keys, 0, $vcount);
        }elseif($kcount < $vcount){
            $vals = array_slice($vals, 0, $kcount);
        }
    } else {
        // more headers than row fields
        if ($kcount > $vcount) {
            // how many fields are we missing at the end of the second array?
            // Add empty strings to ensure arrays $a and $b have same number of elements
            $more = $kcount - $vcount;
            for($i = 0; $i < $more; $i++) {
                $b[] = "";
            }
            // more fields than headers
        } else if ($kcount < $vcount) {
            $more = $vcount - $kcount;
            // fewer elements in the first array, add extra keys
            for($i = 0; $i < $more; $i++) {
                $key = 'extra_field_0' . $i;
                $a[] = $key;
            }

        }
    }

    return array_combine($keys, $vals);
}

function encode_password($pass, $salt = '')
{

    return md5(md5($pass) . $salt);
}

function compare_password($user,$password){
    return encode_password($password,$user['salt'])===$user['password'];
}

function compare_secpassword($user,$password){
    return encode_password($password,md5($user['id']))===$user['secpassword'];
}

function user_log($uid, $action, $result, $remark = '', $tbl = 'member')
{
    return \think\Db::name($tbl . 'Log')->insert(array(
        'create_time' => time(),
        $tbl . '_id' => $uid,
        'ip' => app()->request->ip(),
        'action' => $action,
        'result' => intval($result),
        'remark' => $remark
    ));
}

/**
 * 金额变动
 * charge 充值/赠送 cash 提现/提现失败
 * @param $uid
 * @param $money
 * @param $reson
 * @param string $type
 * @return bool|mixed
 */
function money_log($uid, $money, $reson, $type='',$field='money')
{
    if($money==0)return true;

    $member=\think\Db::name('member')->lock(true)->find($uid);

    if(empty($member))return false;

    if($money>0) {
        $result=\think\Db::name('member')->where(array('id' => $uid))
            ->setInc($field,$money);
    }else{
        $result=\think\Db::name('member')->where(array('id' => $uid))
            ->setDec($field,abs($money));
    }
    if($result) {
        return \think\Db::name('memberMoneyLog')->insert(array(
            'create_time' => time(),
            'member_id' => $uid,
            'type' => $type,
            'before' => $member[$field],
            'amount' => $money,
            'after' => $member[$field] + $money,
            'field'=>$field,
            'reson' => $reson
        ));
    }else{
        return false;
    }
}

function banklist(){
    return array(
        "中国工商银行",
        "招商银行",
        "中国农业银行",
        "中国银行",
        "中国建设银行",
        "中国邮政储蓄银行",
        "中国光大银行",
        "中信银行",
        "交通银行",
        "兴业银行",
        "浦发银行",
        "华夏银行",
        "深圳发展银行",
        "广东发展银行",
        "中国民生银行",
        "恒生银行",
        "汇丰中国银行",
        "渣打中国银行"
    );
}

function payTypes($type = '')
{
    $types = array(
        'wechat' => '微信支付',
        'alipay' => '支付宝支付',
        'unioncard' => '银联卡转帐',
    );
    if (empty($type)) {
        return $types;
    } else {
        return $types[$type];
    }
}

function getPaytypes($type = '')
{
    $where=array();
    if(!empty($type))$where['type']=$type;
    $lists=\think\Db::name('Paytype')->where($where)->select();
    $ptypes=array();
    foreach ($lists as $t){
        $ptypes[$t['id']]=$t;
    }
    return $ptypes;
}

function settingGroups($name = '')
{
    $groups = array(
        'common' => '通用配置',
        'member' => '会员配置',
        'wechat' => '微信配置',
        'advance' => '高级配置'
    );
    if (empty($name)) {
        return $groups;
    } else {
        return $groups[$name];
    }
}

/**
 * 获取配置字段类型
 * @param $key string 类型
 * @return string|array
 */
function settingTypes($key = '')
{
    $types = array();
    $types['text'] = "单行文本";
    $types['number'] = "数字";
    $types['bool'] = "布尔";
    $types['radio'] = "单选";
    $types['check'] = "多选";
    $types['select'] = "下拉选择";
    $types['textarea'] = "多行文本";
    $types['html'] = "编辑器";

    if (empty($key)) {
        return $types;
    } else {
        return isset($types[$key]) ? $types[$key] : '-';
    }
}


/**
 * 显示金额 (除以 100)
 * @param $amount
 * @return float|int
 */
function showmoney($amount){
    return round($amount/100,2);
}

/**
 * 显示卡号
 */
function showcardno($cardno,$pos=6,$fulllen=false){
    $l=strlen($cardno)-$pos;
    return str_repeat('*',$fulllen?$l:3).substr($cardno,$l);
}


function showcashtype($type){
    switch ($type){
        case 'wechat':
            return "微信";
        case 'alipay':
            return "支付宝";
        default:
            return "银行卡";
    }
}


/**
 * 仅转换参数，方便调用
 * @param $time
 * @param $replace
 * @param $format
 * @return false|string
 */
function showdate($time,$replace='-',$format='Y-m-d H:i:s'){
    if(!empty($replace)){
        if($replace!='-'){
            $format=$replace;
            $replace='';
        }
    }
    return $time==0?$replace:date($format,$time);
}

/**
 * 留言状态
 * @param $status int
 * @param bool $wrap
 * @return string
 */
function f_status($status,$wrap=true)
{
    $statusText = array('未审核', '已审核', '隐藏');

    return $wrap?wrap_label($statusText[$status],status_type($status)):$statusText[$status];
}

/**
 * 审核状态
 * @param $status
 * @param bool $wrap
 * @return mixed
 */
function o_status($status,$wrap=true)
{
    $statusText = array('待审核', '确认', '无效');
    return $wrap?wrap_label($statusText[$status],status_type($status)):$statusText[$status];
}

/**
 * 显示状态
 * @param $status
 * @param bool $wrap
 * @return mixed
 */
function v_status($status,$wrap=true)
{
    $statusText = array('隐藏', '显示');
    return $wrap?wrap_label($statusText[$status],status_type($status)):$statusText[$status];
}
function status_type($status){
    return ['warning','success','default'][$status];
}

/**
 * 订单状态
 * @param $status
 * @param bool $wrap
 * @return string
 */
function showstatus($status,$wrap=true){
    switch ($status){
        case "-1":
            return $wrap?wrap_label("已作废",'default'):"已作废";
        case "0":
            return $wrap?wrap_label("待支付",'warning'):"待支付";
        case "1":
            return $wrap?wrap_label("已支付",'danger'):"已支付";
        case "2":
            return $wrap?wrap_label("已发货",'info'):"已发货";
        case "3":
            return $wrap?wrap_label("已完成",'success'):"已完成";

    }
    return $wrap?wrap_label("未知",'default'):'未知';
}
function wrap_label($text,$type='secondary'){
    return "<span class=\"badge badge-$type\">$text</span>";
}

function maskphone($phone){
    $l=strlen($phone);
    return substr($phone,0,3).str_repeat('*',$l-7).substr($phone,$l-4);
}

function random_str($length = 6, $type = 'string', $convert = 0)
{
    $config = array(
        'number' => '1234567890',
        'letter' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
        'string' => 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789',
        'all' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'
    );

    if (!isset($config[$type])) $type = 'string';
    $string = $config[$type];

    $code = '';
    $strlen = strlen($string) - 1;
    for ($i = 0; $i < $length; $i++) {
        $code .= $string{mt_rand(0, $strlen)};
    }
    if (!empty($convert)) {
        $code = ($convert > 0) ? strtoupper($code) : strtolower($code);
    }
    return $code;
}

function getPageGroups($force=false){
    $groups=cache('page_group');
    if(empty($groups) || $force==true){
        $groups=\think\Db::name('PageGroup')->order('sort ASC,id ASC')->select();
        cache('page_group',$groups);
    }
    return $groups;
}

/**
 * 获取排序后的分类
 * @param  array $data [description]
 * @param  integer $pid [description]
 * @param  string $html [description]
 * @param  integer $level [description]
 * @return array
 */
function getSortedCategory(&$data, $pid = 0, $html = "|---", $level = 0)
{
    $temp = array();
    foreach ($data as $k => $v) {
        if ($v['pid'] == $pid) {

            $str = str_repeat($html, $level);
            $v['html'] = $str;
            $temp[] = $v;

            $temp = array_merge($temp, getSortedCategory($data, $v['id'], '|---', $level + 1));
        }

    }
    return $temp;
}


/**
 * 获取全部配置
 * @param $all bool 是否全部数据
 * @param $group bool 是否分组
 * @param $parse bool 是否解析值 针对多选解析成数组
 * @return array
 */
function getSettings($all = false, $group = false, $parse=true)
{
    static $settings;
    if (empty($settings)) {
        $settings = cache('setting');
        if (empty($settings)) {
            $settings = \think\Db::name('setting')->order('sort ASC,id ASC')->select();
            foreach ($settings as $k=>$v){
                if($v['type']=='bool' && empty($v['data']))$v['data']="1:是\n0:否";
                $settings[$k]['data']=parse_data($v['data']);
            }
            cache('setting', $settings);
        }
    }

    $return = array();
    if ($group) {
        foreach ($settings as $set) {
            if (empty($set['group'])) $set['group'] = 'common';
            if (!isset($return[$set['group']])) $return[$set['group']] = array();
            if($parse && $set['type']=='check') {
                $set['value'] = @unserialize($set['value']);
                if(empty($set['value']))$set['value']=array();
            }
            $return[$set['group']][$set['key']] = $all ? $set : $set['value'];
        }
    } else {
        foreach ($settings as $set) {
            if($parse && $set['type']=='check') {
                $set['value'] = @unserialize($set['value']);
                if(empty($set['value']))$set['value']=array();
            }
            $return[$set['key']] = $all ? $set : $set['value'];
        }
    }
    return $return;
}

function getSetting($key){
    $settings=getSettings();
    if(isset($settings[$key]))return $settings[$key];
    else return null;
}
function setSetting($key,$v){
    \think\Db::name('setting')->where(array('key'=>$key))->update(array('value'=>$v));
    cache('setting', null);
}

/**
 * 解析数据
 * @param $d
 * @return array
 */
function parse_data($d){
    if(empty($d))return array();
    $arr=array();
    $darr=preg_split('/[\n\r]+/',$d);
    foreach ($darr as $a){
        $idx=stripos($a,':');
        if($idx>0){
            $arr[substr($a,0,$idx)]=substr($a,$idx+1);
        }else{
            $arr[$a]=$a;
        }
    }
    return $arr;
}


/**
 * 请求接口返回内容
 * @param $url string  [请求的URL地址]
 * @param $params string|bool [请求的参数]
 * @param $ispost int [是否采用POST形式]
 * @return string
 */
function http_request($url, $params = false, $ispost = 0)
{
    $httpInfo = array();
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'JuheData');
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    if(is_array($params)){
        $params=http_build_query($params);
    }
    if ($ispost) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_URL, $url);
    } else {
        if ($params) {
            curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
        } else {
            curl_setopt($ch, CURLOPT_URL, $url);
        }
    }
    $response = curl_exec($ch);
    if ($response === FALSE) {
        //echo "cURL Error: " . curl_error($ch);
        return false;
    }
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $httpInfo = array_merge($httpInfo, curl_getinfo($ch));
    curl_close($ch);
    return $response;
}

function getWeek($d){
    $time=strtotime($d);
    $w=date('N',$time);
    static $weeks=array('','星期一','星期二','星期三','星期四','星期五','星期六','星期日');
    return $weeks[intval($w)];
}

/**
 * 字符串截取
 * @param $str
 * @param $len
 * @param string $dot
 * @return mixed|string
 */
function cutstr($str,$len,$dot='...'){
    $str=html_entity_decode($str);
    $str=strip_tags($str,'');

    $charset = 'utf-8';
    if (strlen($str) <= $len) {
        return $str;
    }
    $str = str_replace(array(' ', '&nbsp;', '&amp;', '&quot;', '&lt;', '&gt;'), array('', ' ','&', '"', '<', '>'), $str);
    $strcut = '';
    if (strtolower($charset) == 'utf-8') {
        $n = $tn = $noc = 0;
        while ($n < strlen($str)) {
            $t = ord($str[$n]);
            if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                $tn = 1; $n++; $noc++;
            } elseif (194 <= $t && $t <= 223) {
                $tn = 2; $n += 2; $noc += 2;
            } elseif (224 <= $t && $t <= 239) {
                $tn = 3; $n += 3; $noc += 2;
            } elseif (240 <= $t && $t <= 247) {
                $tn = 4; $n += 4; $noc += 2;
            } elseif (248 <= $t && $t <= 251) {
                $tn = 5; $n += 5; $noc += 2;
            } elseif ($t == 252 || $t == 253) {
                $tn = 6; $n += 6; $noc += 2;
            } else {
                $n++;
            }
            if($noc >= $len) {
                break;
            }
        }
        if ($noc > $len) {
            $n -= $tn;
        }
        $strcut = substr($str, 0, $n);
    } else {
        for ($i = 0; $i < $len; $i++) {
            $strcut.= ord($str[$i]) > 127 ? $str[$i] . $str[++$i] : $str[$i];
        }
    }
    $strcut = str_replace(array(' ', '&', '"', '<', '>'), array('&nbsp;', '&amp;', '&quot;', '&lt;', '&gt;'), $strcut);
    return $strcut . $dot;
}

function gener_qrcode($text,$size=300,$pad=10,$errLevel='high'){
    $qrCode = new \Endroid\QrCode\QrCode();

    $qrCode->setText($text)
        ->setSize($size)
        ->setPadding($pad)
        ->setErrorCorrection($errLevel)
        ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
        ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
        //->setLabel('thinkphp.cn')
        //->setLabelFontSize(16)
        ->setImageType(\Endroid\QrCode\QrCode::IMAGE_TYPE_PNG);
    $qrCode->render();
}

/**
 * 根据参数裁剪图片
 * @param $file
 * @param $opts
 * @param null|string $savepath
 */
function crop_image($file,$opts,$savepath=null){
    $img = trim($file);
    $imgWidth = (int)$opts['w'];
    $imgHeight = (int)$opts['h'];
    $imgQuality = (int)$opts['q'];
    $imgMode = strtolower(trim($opts['m']));

    if (empty($img)){
        exit();
    }
    if($imgWidth<1 && $imgHeight<1){
        $imgWidth = 150;
    }
    if($imgQuality<1){
        $imgQuality = 80;
    }

//$img = str_replace($img,'http://' . strtolower($_SERVER["SERVER_NAME"]),"");

    $imgData=getImgData($img);
    $image=imagecreatefromstring($imgData);

//list($photoWidth ,$photoHeight) = getimagesize($img);
    $photoWidth = imagesx($image);
    $photoHeight = imagesy($image);

    if($photoWidth>0 And $photoHeight>0 ){
        if($photoWidth > $imgWidth Or $photoHeight > $imgHeight){
            $photoScale=$photoWidth/$photoHeight;
            if ($imgWidth>0 And $imgHeight>0) {
                $imgScale=$imgWidth/$imgHeight;
            }else{
                $imgScale=$photoScale;
            }
            $clipLeft=0;
            $clipTop=0;
            switch($imgMode){
                case "o":
                case "outer":
                    if ($photoScale == $imgScale){
                        if ($imgWidth>0){
                            $tempWidth = $photoWidth;
                            $tempHeight = $tempWidth/$imgScale;
                        }else{
                            $tempHeight = $photoHeight;
                            $tempWidth = $tempHeight * $imgScale;
                        }
                    }elseif($photoScale>$imgScale) {
                        $tempHeight = $photoHeight;
                        $tempWidth = $tempHeight * $imgScale;
                        $clipLeft=($photoWidth-$tempWidth)*.5;
                    }else{
                        $tempWidth=$photoWidth;
                        $tempHeight = $tempWidth/$imgScale;
                        $clipTop=($photoHeight-$tempHeight)*.5;
                    }
                    break;
                default:
                    if ($photoScale == $imgScale){
                        if ($imgWidth>0){
                            $tempWidth = $imgWidth;
                            $tempHeight = $imgWidth/$imgScale;
                        }else{
                            $tempHeight = $imgHeight;
                            $tempWidth = $imgHeight * $imgScale;
                        }
                    }elseif ($photoScale>$imgScale){
                        $tempWidth=$imgWidth;
                        $tempHeight = $imgWidth/$photoScale;
                    }else{
                        $tempHeight = $imgHeight;
                        $tempWidth = $imgHeight * $photoScale;
                    }
            }

            if ($clipLeft>0 Or $clipTop>0){
                $newimg=imagecreatetruecolor($imgWidth, $imgHeight);
                imagecopyresampled($newimg, $image, 0, 0, $clipLeft, $clipTop, $imgWidth, $imgHeight, $tempWidth, $tempHeight);
                //imagecopyresized($newimg, $image, 0, 0, $clipLeft, $clipTop, $imgWidth, $imgHeight, $tempWidth, $tempHeight);
            }else{
                $newimg=imagecreatetruecolor($tempWidth, $tempHeight);
                imagecopyresampled($newimg, $image, 0, 0, 0, 0, $tempWidth, $tempHeight, $photoWidth, $photoHeight);
                //imagecopyresized($newimg, $image, 0, 0, 0, 0, $tempWidth, $tempHeight, $photoWidth, $photoHeight);
            }
            imagedestroy($image);

            outputImage($newimg,$savepath,$imgQuality);
        }else{
            outputImage($image,$savepath,$imgQuality);
        }
    }
}


/**
 * 输出图片
 * @param $image
 * @param $savepath
 * @param $imgQuality
 */
function outputImage($image,$savepath=null,$imgQuality=80){
    header("Content-type: " . image_type_to_mime_type(IMAGETYPE_JPEG));
    imagejpeg($image,$savepath,$imgQuality);
    imagedestroy($image);
}


/**
 * 获取文件内容
 * @param $img
 * @return bool|string
 */
function getImgData($img){
    if(strripos($img, 'http://')!==FALSE OR strripos($img,'https://') !==FALSE) {	//站外图片
        $data=file_get_contents($img);
    }else{	//站内图片
        $data=file_get_contents(DOC_ROOT.'/'.$img);
    }
    return $data;
}
//end file