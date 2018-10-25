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

/** =====================================  固态数据类函数  ===================================== **/
function getTextStyles(){
    return ['secondary','primary','info','success','warning','danger'];
}
function getMoneyFields(){
    $fields= [
        'all'=>'不限',
        'money'=>'消费积分',
        'credit'=>'现金积分'
    ];
    return $fields;
}
function getLogTypes(){
    return [
        'consume'=>'消费',
        'recharge'=>'充值'
    ];
}
function getMemberTypes(){
    return [
        1=>'普通会员',
        2=>'内部员工'
    ];
}
function getArticleTypes(){
    return [
        1=>'普通',
        2=>'置顶',
        3=>'热门',
        4=>'推荐'
    ];
}
function getProductTypes(){
    return [
        1=>'普通'
    ];
}
function getOauthTypes(){
    return [
        'facebook' => 'Facebook',
        'github' => 'GitHub',
        'google' => 'Google',
        'linkedin' => 'Linkedin',
        'weibo' => 'Weibo',
        'qq' => 'QQ',
        'wechat' => 'WeChat',
        'douban' => 'Douban',
        'wework' => 'WeWork',
        'outlook' => 'Outlook',
    ];
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
    $types['location'] = "位置选择";
    $types['html'] = "编辑器";

    if (empty($key)) {
        return $types;
    } else {
        return isset($types[$key]) ? $types[$key] : '-';
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

/** =====================================  数据类函数  ===================================== **/

function getMemberLevels()
{
    static $levels;
    if (empty($levels)) {
        $levels = cache('levels');
        if (empty($levels)) {
            $model=new \app\admin\model\MemberLevelModel();
            $data =  $model->order('sort ASC,level_id ASC')->select();
            $levels=array_index($data,'level_id');
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
    $from_id=0;
    if(is_array($uid)){
        $from_id=intval($uid[1]);
        $uid=$uid[0];
    }

    $member=\think\Db::name('member')->lock(true)->find($uid);

    if(empty($member))return false;

    if($money>0) {
        $result=\think\Db::name('member')->where('id' , $uid)
            ->setInc($field,$money);
    }else{
        if($member[$field]<abs($money))return false;
        $result=\think\Db::name('member')->where('id' , $uid)
            ->setDec($field,abs($money));
    }
    if($result) {
        return \think\Db::name('memberMoneyLog')->insert(array(
            'create_time' => time(),
            'member_id' => $uid,
            'from_member_id'=>$from_id,
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

function getMemberParents($userid,$level=5,$getid=true){
    $parents=[];
    $user=\think\Db::name('Member')->where('id',$userid)->field('id,level_id,username,referer')->find();
    $layer=0;
    while(!empty($user)){
        $layer++;
        $userid=$user['referer'];
        if(!$userid)break;
        $user=\think\Db::name('Member')->where('id',$userid)->field('id,level_id,username,referer')->find();
        $parents[] = $getid?$userid:$user;
        if($level>0 && $layer>=$level)break;
    }
    return $parents;
}

function getMemberSons($userid,$level=1,$getid=true){
    $sons=[];
    $users=\think\Db::name('Member')->where('referer',$userid)->field('id,level_id,username,referer')->select();
    $layer=0;
    while(!empty($users)){
        $layer++;
        $userids=array_column($users,'id');
        $sons = array_merge($sons, $getid?$userids:$users);
        if($level>0 && $layer>=$level)break;
        $users=\think\Db::name('Member')->whereIn('referer',$userids)->field('id,level_id,username,referer')->select();
    }
    return $sons;
}


/** =====================================  功能类函数  ===================================== **/

function is_wechat_avatar($avatar){
    if(empty($avatar)){
        return false;
    }
    $nothttps=str_replace('https://','http://',$avatar);
    if(strpos($nothttps,'http://thirdwx.qlogo.cn/')===0){
        return true;
    }
    if(strpos($nothttps,'http://wx.qlogo.cn/')===0){
        return true;
    }

    return false;
}

function get_redirect($default=''){
    $redirect=redirect()->restore();
    $urldata=$redirect->getData();
    if(empty($urldata)){
        if(empty($default))$default=url('index/member/index');
        return redirect($default);
    }
    return $redirect;
}

function current_url($withqry=true){
    $query=$withqry?$_REQUEST['QUERY_STRING']:'';
    if(!empty($query))$query='?'.$query;
    return current_domain().$_SERVER['REQUEST_URI'].$query;
}

function current_domain(){
    return ($_SERVER['HTTPS']=="On"?"https":"http").'://'.$_SERVER['SERVER_NAME'];
}

/**
 * 替换数组中的一个或几个键名对应的值
 */
function searchKey($key,$val,$search=''){
    if(!is_array($search)){
        $search=request()->param();
    }
    if(strpos($key,',')>0){
        $keys=explode(',',$key);
        foreach ($keys as $key){
            $search=searchKey(trim($key),$val,$search);
        }
    }else {
        if (empty($val) || $val == 'all') {
            if (isset($search[$key])) unset($search[$key]);
        } else {
            $search[$key] = $val;
        }
    }
    return $search;
}

/**
 * 过滤特殊字符
 */
function filter_specchar($str){
    return preg_replace("/\/|\~|\!|\@|\#|\\$|\%|\^|\&|\*|\(|\)|\_|\+|\{|\}|\:|\<|\>|\?|\[|\]|\,|\.|\/|\;|\'|\`|\-|\=|\\\|\|/",'',$str);
}

/**
 * id参数转换成数组,用于批量操作
 */
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
 * 索引二维数组
 * @param $arr array 二维数组
 * @param $index  string  索引
 * @param bool $ismulti 一对多模式
 * @return array
 */
function array_index($arr,$index,$ismulti=false){
    $return=[];
    if(!empty($arr)) {
        $val = '';
        if (strpos($index, ',') > 0) {
            $indexes = explode(',', $index);
            $index = trim($indexes[0]);
            $val = trim($indexes[1]);
        }
        foreach ($arr as $row) {
            if ($ismulti) {
                $return[$row[$index]][] = empty($val) ? $row : $row[$val];
            } else {
                $return[$row[$index]] = empty($val) ? $row : $row[$val];
            }
        }
    }
    return $return;
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

function implode_cmp($arr,$glue=','){
    if(is_array($arr)){
        return implode($glue,$arr);
    }
    return '';
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

function show_price($row){
    $price=$row['min_price'];
    if($row['max_price']>$row['min_price']){
        $price .= ' ~ '.$row['min_price'];
    }
    return $price;
}

function fix_in_array($val,$arr){
    if(empty($arr))return false;
    return in_array($val,(array)$arr);
}

function array_max($arr,$column){
    if(empty($arr))return 0;
    $data=array_column($arr,$column);
    return max($data);
}
function array_min($arr,$column){
    if(empty($arr))return 0;
    $data=array_column($arr,$column);
    return min($data);
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
function feedback_status($status,$wrap=true)
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
function audit_status($status,$wrap=true)
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
function show_status($status,$wrap=true)
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
function order_status($status,$wrap=true){
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
function money_type($type,$wrap=true){
    switch ($type){
        case "money":
            return $wrap?wrap_label("余额",'success'):"余额";
        case "credit":
            return $wrap?wrap_label("积分",'info'):"积分";

    }
    return $wrap?wrap_label("未知币种",'default'):'未知币种';
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
 * @param  string $pre [description]
 * @return array
 */
function getSortedCategory(&$data, $pid = 0, $pre = "")
{
    $temp = array();
    $curdata=array_filter($data,function($item) use ($pid){
        return $item['pid']==$pid;
    });

    $count=count($curdata);

    $idx=0;
    foreach ($curdata as $v) {
        $idx++;
        $islast=$idx==$count?true:false;
        $v['html'] =$islast?($pre.'└─'):($pre.'├─');
        $temp[] = $v;
        if($islast){
            $temp = array_merge($temp, getSortedCategory($data, $v['id'], $pre.'　　'));
        }else{
            $temp = array_merge($temp, getSortedCategory($data, $v['id'], $pre.'│　'));
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
    \think\Db::name('setting')->where('key',$key)->update(array('value'=>$v));
    cache('setting', null);
}

/**
 * 解析数据
 * @param $d
 * @return array
 */
function parse_data($d)
{
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

function serialize_data($arr)
{
    $str=[];
    foreach($arr as $k=>$v){
        if($k==$v){
            $str[]=$v;
        }else{
            $str[]=$k.':'.$v;
        }
    }
    return implode("\n",$str);
}

function format_date($date_str, $format){
    $time=strtotime($date_str);
    if($time>0){
        return date($format,$time);
    }
    return '';
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
    $str = str_replace(array( '&nbsp;', '&amp;', '&quot;', '&lt;', '&gt;'), array(' ','&', '"', '<', '>'), $str);
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
    $strcut = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);
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

function file_rule($file){
    static $stamp='';
    if(!$stamp)$stamp=microtime();
    else $stamp+=1;
    return md5(md5_file($file).$stamp);
}

function crop_image($file, $options){
    $imageCrop=new \extcore\ImageCrop($file, $options);
    return $imageCrop->crop();
}

//end file