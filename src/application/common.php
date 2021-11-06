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

define('ART_TYPE_NORMAL',1);
define('ART_TYPE_TOP',2);
define('ART_TYPE_HOT',4);
define('ART_TYPE_RECOMMEND',8);

define('PRO_TYPE_NORMAL',1);
define('PRO_TYPE_UPGRADE',2);
define('PRO_TYPE_BIND',4);

function writelog($message,$type=\think\Log::INFO){
    if(config('app_debug')==true){
        \think\facade\Log::record($message,$type);
    }
}

function force_json_decode($string){
    if(is_array($string))return $string;
    if(empty($string))return [];
    
    $json = @json_decode($string,true);
    return empty($json)?[]:$json;
}

/**
 * 根据配置输出本地图片路径或远程oss路径
 * @param $src
 * @param string $width
 * @param string $height
 * @param int $quality
 * @return string
 */
function media($src,$width='',$height='',$quality=70){
    if(empty($src))return $src;
    $root = config('template.oss_root');
    $src = ltrim($src,'.');
    if(empty($root)){
        return $src;
    }else {
        return $root . $src;
    }
}

/**
 * 完整输出附件的url
 * @param $src
 * @return string
 */
function local_media($src){
    if(empty($src))return $src;
    $src = ltrim($src,'.');
    if(strpos($src,'/')===0 || strpos($src,'://')===false){
        return url('/','',false,true).$src;
    }
    return $src;
}

/**
 * tp已支持下载输出
 * @param $data
 * @param $filename
 * @param $isContent
 * @param $mime
 * @return \think\Response
 */
function file_download($data,$filename='',$isContent=true,$mime=''){
    /**
     * @var think\response\Download
     */
    $response = \think\Response::create($data?:$filename, 'download', 200);
    if($mime){
        $response->mimeType($mime);
    }
    if($isContent){
        $response->isContent(true);
    }
    if($filename){
        $response->name($filename);
    }
    return $response;
}

/** =====================================  固态数据类函数  ===================================== **/
/**
 * 小标签的样式
 * @return array
 */
function getTextStyles(){
    return ['secondary','primary','info','success','warning','danger'];
}
function getMoneyFields($withall=true){
    $fields= [
        'all'=>lang('All'),
        'money'=>lang('Balance'),
        'credit'=>lang('Credit'),
        'reward'=>lang('Reward')
    ];
    if(!$withall)unset($fields['all']);
    return $fields;
}

function getLogTypes($withall=true, $filter = null){
    $fields = [
        'all'=>lang('All'),
        'system'=>lang('System Opt.'),
        'consume'=>lang('Consume'),
        'recharge'=>lang('Recharge'),
    ];
    if(!$withall)unset($fields['all']);
    if($filter !== null){
        if(is_string($filter)){
            $filter = array_map('trim',explode(',', $filter));
        }
        foreach($fields as $k=>$v){
            if($k == 'all')continue;
            if(!in_array($k, $filter)){
                unset($fields[$k]);
            }
        }
    }
    return $fields;
}
function getMemberTypes(){
    return [
        1=>lang('Member'),
        2=>lang('Employee')
    ];
}

function getWechatTypes(){
    return [
        'subscribe'=>'关注回复消息',
        'resubscribe'=>'二次关注回复消息',
        'default'=>'默认回复',
        'keyword'=>'关键字回复',
        'click'=>'点击事件回复'
    ];
}
function getWechatReplyTypes(){
    return [
        'text'=>'文本消息',
        'news'=>'图文消息',
        'image'=>'图片消息',
        'custom'=>'托管消息'
    ];
}
function getWechatMaterialTypes(){
    return [
        'image'=>'图片',
        'voice'=>'语音',
        'video'=>'视频',
        'article'=>'图文'
    ];
}
function getArticleTypes(){
    return [
        ART_TYPE_NORMAL=>lang('Normal'),
        ART_TYPE_TOP=>lang('Top'),
        ART_TYPE_HOT=>lang('Hot'),
        ART_TYPE_RECOMMEND=>lang('Recommend')
    ];
}
function getProductTypes(){
    return [
        PRO_TYPE_NORMAL=>lang('Normal'),
        PRO_TYPE_UPGRADE=>lang('Price Upgrade'),
        PRO_TYPE_BIND=>lang('Bind Upgrade')
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
        'wechat_open' => 'Wechat Open',
        'douban' => 'Douban',
        'wework' => 'WeWork',
        'outlook' => 'Outlook',
    ];
}



function settingGroups($name = '')
{
    $groups = array(
        'common' => lang('Common Settings'),
        'member' => lang('Member Settings'),
        'third' => lang('Third Settings'),
        'sign' => lang('Sign Settings'),
        'advance' => lang('Advance Settings'),
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
    $types['text'] = lang('Text Field');
    $types['number'] = lang('Numberic');
    $types['bool'] = lang('True False');
    $types['radio'] = lang('Radio Box');
    $types['check'] = lang('Check Box');
    $types['select'] = lang('Select');
    $types['textarea'] = lang('Textarea');
    $types['location'] = lang('Location Picker');
    $types['image'] = lang('Upload Image');
    $types['json'] = lang('Json');
    $types['array'] = lang('Array');
    $types['html'] = lang('Editor');

    if (empty($key)) {
        return $types;
    } else {
        return isset($types[$key]) ? $types[$key] : '-';
    }
}

function banklist($withcode=false){
    $banklist = array(
        '工商银行'=>'1002',
        '农业银行'=>'1005',
        '中国银行'=>'1026',
        '建设银行'=>'1003',
        '招商银行'=>'1001',
        '邮储银行'=>'1066',
        '交通银行'=>'1020',
        '浦发银行'=>'1004',
        '民生银行'=>'1006',
        '兴业银行'=>'1009',
        '平安银行'=>'1010',
        '中信银行'=>'1021',
        '华夏银行'=>'1025',
        '广发银行'=>'1027',
        '光大银行'=>'1022',
        '北京银行'=>'4836',
        '宁波银行'=>'1056',
        '上海银行'=>'1024',

    );
    if($withcode){
        return $banklist;
    }
    return array_keys($banklist);
}

function payTypes($type = '')
{
    $types = array(
        'wechat' => lang('Wechat Pay'),
        'alipay' => lang('Alipay '),
        'unioncard' => lang('Unionpay'),
    );
    if (empty($type)) {
        return $types;
    } else {
        return $types[$type];
    }
}


/** =====================================  模板类函数  ===================================== **/

/**
 * 显示金额 (除以 100)
 * @param $amount
 * @return float|int
 */
function showmoney($amount){
    return number_format(round($amount/100,2),2);
}

/**
 * 显示卡号
 * @param $cardno
 * @param int $pos
 * @param bool $fulllen
 * @return string
 */
function showcardno($cardno,$pos=6,$fulllen=false){
    $l=strlen($cardno)-$pos;
    return str_repeat('*',$fulllen?$l:3).substr($cardno,$l);
}

function fmtCardno($cardno){
    return $cardno;
}

function showcashtype($type){
    switch ($type){
        case 'wechat':
            return lang('Wechat');
        case 'alipay':
            return lang('Alipay');
        default:
            return lang('Unioncard');
    }
}

/**
 * 根据数据的最低价和最高价显示范围
 * @param $row
 * @return string
 */
function show_price($row){
    $price=$row['min_price'];
    if($row['max_price']>$row['min_price']){
        $price .= ' ~ '.$row['min_price'];
    }
    return $price;
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
    $statusText = array(lang('Unaudited'), lang('Audited'), lang('Hidden'));

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
    $statusText = array(lang('Unaudited'), lang('Confirmed'), lang('Invalid'));
    $statusText[-1]=lang('Invalid');
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
    $statusText = array(lang('Hidden'), lang('Shown'));
    $statusText[-1]=lang('Invalid');
    return $wrap?wrap_label($statusText[$status],status_type($status)):$statusText[$status];
}
function status_type($status){
    return $status>=0?['warning','success','secondary'][$status]:'secondary';
}

/**
 * 获取订单状态
 * @param $status
 * @return string
 */
function get_order_status($status){
    switch ($status){
        case "-3":
            return lang('Refunding');
        case "-2":
            return lang('Cancelled');
        case "-1":
            return lang('Invalid');
        case "0":
            return lang('Unpaid');
        case "1":
            return lang('Unshipped');
        case "2":
            return lang('Unreceived');
        case "3":
            return lang('Unevaluate');
        case "4":
            return lang('Completed');
        default:
            return lang('Unknown');
    }
    return lang('Unknown');
}
/**
 * 订单状态
 * @param $status
 * @param bool $wrap
 * @return string
 */
function order_status($status,$wrap=true){
    $style='secondary';
    switch ($status){
        case "0":
            $style='warning';
            break;
        case "1":
            $style='danger';
            break;
        case "2":
            $style='secondary';
            break;
        case "3":
            $style='info';
            break;
        case "4":
            $style='success';
            break;
        default:
            $style='secondary';

    }
    return $wrap?wrap_label(get_order_status($status),$style):get_order_status($status);
}
function money_type($type,$wrap=true){
    switch ($type){
        case "money":
            return $wrap?wrap_label(lang('Balance'),'success'):lang('Balance');
        case "credit":
            return $wrap?wrap_label(lang('Credit'),'info'):lang('Credit');
        case "reward":
            return $wrap?wrap_label(lang('Reward'),'warning'):lang('Reward');
        default:
            return $wrap?wrap_label(lang('Unknown'),'secondary'):lang('Unknown');

    }
    return $wrap?wrap_label(lang('Unknown'),'secondary'):lang('Unknown');
}
function award_status($status,$wrap=true){
    switch ($status){
        case "1":
            return $wrap?wrap_label(lang('Gived'),'success'):lang('Gived');
        case "-1":
            return $wrap?wrap_label(lang('Canceled'),'secondary'):lang('Canceled');
        case "0":
            return $wrap?wrap_label(lang('Waiting'),'warning'):lang('Waiting');
        default:
            $wrap?wrap_label(lang('Unknown'),'default'):lang('Unknown');
    }
    return $wrap?wrap_label(lang('Unknown'),'default'):lang('Unknown');
}
function wrap_label($text,$type='secondary'){
    return "<span class=\"badge badge-$type\">$text</span>";
}

function print_remark($data){
    if(!empty($data) && !is_array($data)){
        $datarr = @json_decode($data);
    }
    if(is_array($datarr)){
        $temp = array_shift($datarr);
        return call_user_func('lang', $temp, $datarr);
    }
    return $data;
}

function masktext($text, $prelen=3,$suflen=4,$midmax=4){
    $l=mb_strlen($text);
    $masklen=min($l-$prelen-$suflen,$midmax);
    return mb_substr($text,0,$prelen).str_repeat('*',$masklen).mb_substr($text,$l-$suflen);
}

function maskphone($phone){
    $l=strlen($phone);
    $masklen=min($l-7,4);
    return substr($phone,0,3).str_repeat('*',$masklen).substr($phone,$l-4);
}

function maskemail($email){
    if(empty($email) || strpos($email,'@')<1)return '';
    $part=explode('@',$email);
    $l=strlen($part[0]);
    if($l<=3)$l=5;
    return substr($part[0],0,3).str_repeat('*',$l-3).'@'.$part[1];
}

function getWeek($d){
    $time=strtotime($d);
    $w=date('N',$time);
    static $weeks=array('','星期一','星期二','星期三','星期四','星期五','星期六','星期日');
    return $weeks[intval($w)];
}

/**
 * 过滤emoji字符
 * @param $str
 * @param $replace
 * @return mixed
 */
function filter_emoji($str, $replace = '')
{
    $str = preg_replace_callback( '/./u',
        function (array $match) use ($replace) {
            return strlen($match[0]) >= 4 ? $replace : $match[0];
        },
        $str);
    return $str;
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
    $str = preg_replace('/\s+/',' ',$str);
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
    $strcut = str_replace(array('"', '<', '>'), array( '&quot;', '&lt;', '&gt;'), $strcut);
    return $strcut . $dot;
}

/**
 * 普通文本转换为段落
 * @param mixed $string 
 * @return string|string[] 
 */
function nl2p($string){
    if(empty($string))return '';

    $html = "<p>".implode("</p><p>", array_map('trim', explode("\n", trim($string))))."</p>";

    return str_replace('<p></p>','',$html);
}

/** =====================================  数据类函数  ===================================== **/

/**
 * 获取翻译到指定语言的数据
 * @param $data
 * @param $table
 * @param string $key
 * @param string $lang 默认当前语言
 * @return array
 */
function translate($data,$table,$key='id',$lang=''){
    return \app\common\facade\TranslateFacade::trans_list($data,$table,$key,$lang);
}

/**
 * 获取指定行或字段的翻译
 * @param $table
 * @param $key
 * @param $field
 * @param string $lang
 * @return array|string
 */
function get_translate($table,$key,$field='',$lang=''){
    return \app\common\facade\TranslateFacade::get_trans($table,$key,$field,$lang);
}


function getMemberLevels($force=false)
{
    return \app\common\model\MemberLevelModel::getCacheData($force);
}
function getMemberLevel($level_id){
    $levels=getMemberLevels();
    return $levels[$level_id]?:[];
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



/**
 * 操作日志
 * @param $uid
 * @param $action string|array 操作名 或 [操作名,关联id]
 * @param $result bool 操作结果
 * @param string|array $remark
 * @param string $tbl
 * @return int|string
 */
function user_log($uid, $action, $result, $remark = '', $tbl = 'member')
{
    $other_id=0;
    if(is_array($action)){
        $other_id=$action[1];
        $action=$action[0];
    }
    $datas=[];

    $data=[
        'create_time' => time(),
        $tbl . '_id' => $uid,
        'other_id'=>$other_id,
        'ip' => app()->request->ip(),
        'action' => $action,
        'result' => intval($result),
        'remark' => json_encode(is_array($remark)?$remark:[$remark],JSON_UNESCAPED_UNICODE)
    ];
    if($tbl==='member'){
        $data['model']=request()->module();
    }
    if(is_array($other_id)){
        foreach ($other_id as $id){
            $data['other_id']=$id;
            $datas[] = $data;
        }
    }else {
        $datas[] = $data;
    }
    return \think\Db::name($tbl . 'Log')->insertAll($datas);
}

/**
 * 金额变动 支持批量处理
 * charge 充值/赠送 cash 提现/提现失败
 * @param $uid
 * @param $money
 * @param $reson
 * @param string $type
 * @param int $from_id
 * @param string $field
 * @return bool|mixed
 */
function money_log($uid, $money, $reson, $type='',$from_id=0,$field='money')
{
    if($money==0)return true;
    $fields=getMoneyFields();
    if(is_string($from_id) && isset($fields[$from_id])){
        $field=$from_id;
        $from_id=0;
    }
    if($field=='all' || !isset($fields[$field]))return false;

    if(!is_array($uid)){
        $uid=idArr($uid);
    }
    if(count($uid)>500){
        $uid_groups=array_chunk($uid,500);
        $returns=[];
        foreach ($uid_groups as $uids){
            $returns=array_merge($returns,money_log($uids, $money, $reson, $type,$from_id,$field));
        }
        return $returns;
    }

    $logs=[];
    $members=\think\Db::name('member')->field('id,username,'.$field)
        ->whereIn('id' , $uid)->select();
    $time=time();
    if($money>0) {
        $result=\think\Db::name('member')->whereIn('id' , $uid)
            ->setInc($field,$money);
        foreach ($members as $member){
            $logs[]=[
                'create_time' => $time,
                'member_id' => $member['id'],
                'from_member_id'=>$from_id,
                'type' => $type,
                'before' => $member[$field],
                'amount' => $money,
                'after' => $member[$field] + $money,
                'field'=>$field,
                'reson' => $reson
            ];
        }
    }else{
        $decMoney=abs($money);
        $result=\think\Db::name('member')->whereIn('id' , $uid)
            ->where($field,'>=',$decMoney)
            ->setDec($field,$decMoney);
        foreach ($members as $member){
            if($member[$field]>=$decMoney) {
                $logs[] = [
                    'create_time' => time(),
                    'member_id' => $member['id'],
                    'from_member_id' => $from_id,
                    'type' => $type,
                    'before' => $member[$field],
                    'amount' => $money,
                    'after' => $member[$field] + $money,
                    'field' => $field,
                    'reson' => $reson
                ];
            }
        }

    }
    if($result) {
        \think\Db::name('memberMoneyLog')->insertAll($logs);
        return array_column($logs,'member_id');
    }else{
        return false;
    }
}

function money_force_log($uid, $money, $reson, $type='',$from_id=0,$field='money')
{
    if($money==0)return true;
    $fields=getMoneyFields();
    if($field=='all' || !isset($fields[$field]))return false;

    if(is_array($uid)){
        throw new Exception('This method do not support batch uid');
    }

    $member=\think\Db::name('member')->field('id,username,'.$field)
        ->where('id' , $uid)->find();
    $time=time();
    if($money>0) {
        $result=\think\Db::name('member')->where('id' , $uid)
            ->setInc($field,$money);
        $log=[
            'create_time' => $time,
            'member_id' => $member['id'],
            'from_member_id'=>$from_id,
            'type' => $type,
            'before' => $member[$field],
            'amount' => $money,
            'after' => $member[$field] + $money,
            'field'=>$field,
            'reson' => $reson
        ];
    }else{
        $decMoney=abs($money);
        $result=\think\Db::name('member')->where('id' , $uid)
            ->setDec($field,$decMoney);
        $log = [
            'create_time' => time(),
            'member_id' => $member['id'],
            'from_member_id' => $from_id,
            'type' => $type,
            'before' => $member[$field],
            'amount' => $money,
            'after' => $member[$field] + $money,
            'field' => $field,
            'reson' => $reson
        ];

    }
    if($result) {
        return \think\Db::name('memberMoneyLog')->insert($log,false,true);
    }else{
        return false;
    }
}

function getPaytypes($type = '')
{

    $model=\think\Db::name('Paytype');
    if(!empty($type)){
        $model->where('type',$type);
    }
    $lists=$model->select();
    return array_index($lists,'id');
}

function getMemberParents($userid,$level=5,$getid=true){
    return \app\common\model\MemberModel::getParents($userid,$level,$getid);
}

function getMemberSons($userid,$level=1,$getid=true){
    return \app\common\model\MemberModel::getSons($userid, $level, $getid);
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
 * 获取全部配置
 * @param $all bool 是否全部数据
 * @param $group bool 是否分组
 * @param $parse bool 是否解析值 针对多选解析成数组
 * @return array
 */
function getSettings($all = false, $group = false, $parse=true)
{
    return \app\common\model\SettingModel::getSettings($all, $group, $parse);
}

function getSetting($key){
    $settings=getSettings();
    if(isset($settings[$key]))return $settings[$key];
    else return null;
}
function setSetting($key,$v){
    \think\Db::name('setting')->where('key',$key)->update(array('value'=>$v));
    \app\common\model\SettingModel::clearCache();
}



/** =====================================  功能类函数  ===================================== **/

/**
 * 判断头像图片是否微信头像
 * 以确定更新用户资料时是否更新头像
 * @param $avatar
 * @return bool
 */
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

/**
 * tp中已支持restore传入默认参数
 * @param string $default
 * @return $this|\think\response\Redirect
 */
function get_redirect($default=''){
    if(empty($default))$default=url('index/member/index');
    return redirect()->restore($default);
}

function current_url($withqry=true){
    $query=$withqry?$_REQUEST['QUERY_STRING']:'';
    if(!empty($query))$query='?'.$query;
    return current_domain().$_SERVER['REQUEST_URI'].$query;
}

function current_domain(){
    return rtrim(url('/','',false,true),'/');
}

/**
 * 判断当前是否在某控制器内,并且不是某些action
 * @param string $controller 
 * @param array|string $except_methods 
 * @return bool 
 */
function is_controller($controller, $except_actions=[]){
    if(is_string($except_actions)){
        $except_actions=explode(',',$except_actions);
    }
    return strcasecmp(request()->controller(), $controller) == 0 && !in_array(request()->action(),$except_actions);
}

/**
 * 判断当前是否在某个控制器内的某些action
 * @param string $controller 
 * @param array|string $actions 
 * @return bool 
 */
function is_action($controller,$actions){
    if(is_string($actions)){
        $actions = explode(',', $actions);
    }
    return strcasecmp(request()->controller(), $controller) == 0 &&
        in_array(request()->action(),$actions);
}

/**
 * 用于url的base64编码和解码功能
 * @param mixed $text 
 * @return string 
 */
function base64url_encode($text) {
    if(empty($text))return '';
    $base64 = base64_encode($text);
    $base64url = strtr($base64, '+/=', '-_,');
    return $base64url;
}

function base64url_decode($text) {
    if(empty($text))return '';
    $base64url = strtr($text, '-_,', '+/=');
    $base64 = base64_decode($base64url);
    return $base64;
}

/**
 * 带权重的数组随机
 * @param $array
 * @param string $wfield
 * @param bool $order 正向权重
 * @return array
 */
function weight_random($array, $wfield='weight',$order=true){

    $row=[];
    if(empty($array))return $row;
    if(!$order){
        $max = max(array_column($array,$wfield));
        $pow = pow(10,ceil(log10($max)));
        $ofield = $wfield;
        $wfield='_new_'.$wfield;
        foreach ($array as &$row){
            $row[$wfield]=$row[$ofield]==0?$pow:($pow/$row[$ofield]);
        }
        unset($row);
    }
    $total = array_sum(array_column($array,$wfield));
    $randmax = min($total * 1000, mt_getrandmax());
    $rand = mt_rand(0,$randmax);
    $rand = $rand * $total / $randmax;
    $sum=0;
    foreach ($array as $row){
        $sum += $row[$wfield];
        if($sum>=$rand){
            return $row;
        }
    }
    return $row;
}

/**
 * 替换数组中的一个或几个键名对应的值
 * @param $key
 * @param $val
 * @param string $search
 * @return array|mixed|string
 */
function searchKey($key,$val,$search=''){
    if(!is_array($search)){
        $search=request()->param();
    }
    if(isset($search['page'])){
        unset($search['page']);
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
 * 主要用于搜索关键字的过滤
 * @param $str
 * @return mixed
 */
function filter_specchar($str){
    return preg_replace("/\/|\~|\!|\@|\#|\\$|\%|\^|\&|\*|\(|\)|\_|\+|\{|\}|\:|\<|\>|\?|\[|\]|\,|\.|\/|\;|\'|\`|\-|\=|\\\|\|/",'',$str);
}

/**
 * id参数转换成数组
 * 用于数据库主键批量操作
 * @param $id
 * @return array
 */
function idArr($id){
    if(is_array($id)){
        return array_map(function($i){
            return intval($i);
        },$id);
    }
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

define('COMBINE_PAD_NONE',0);
define('COMBINE_PAD_VALUE',1);
define('COMBINE_PAD_KEY',2);
/**
 * 兼容的array_combine
 * @param array $keys
 * @param array $vals
 * @param int $pad_mode
 * @param int|string|mixed $value_default
 * @param string $key_prefix
 * @return array
 */
function array_combine_cmp($keys,$vals,$pad_mode=0, $value_default="",$key_prefix='extra_field_'){
    $kcount = count($keys);
    $vcount = count($vals);
    if (!$pad_mode) {
        if($kcount > $vcount) {
            $keys = array_slice($keys, 0, $vcount);
        }elseif($kcount < $vcount){
            $vals = array_slice($vals, 0, $kcount);
        }
    } else {
        // more headers than row fields
        if ($kcount > $vcount && ($pad_mode & COMBINE_PAD_VALUE)==COMBINE_PAD_VALUE) {
            // how many fields are we missing at the end of the second array?
            // Add empty strings to ensure arrays $a and $b have same number of elements
            $more = $kcount - $vcount;
            for($i = 0; $i < $more; $i++) {
                $vals[] = $value_default;
            }
            // more fields than headers
        } else if ($kcount < $vcount && ($pad_mode & COMBINE_PAD_KEY)==COMBINE_PAD_KEY) {
            $more = $vcount - $kcount;
            // fewer elements in the first array, add extra keys
            for($i = 0; $i < $more; $i++) {
                $key = $key_prefix . $i;
                $keys[] = $key;
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

function encode_password($pass, $salt = '')
{
    return md5(md5($pass) . $salt);
}

function compare_password($user,$password){
    return encode_password($password,$user['salt'])===$user['password'];
}

function compare_secpassword($user,$password){
    return encode_password($password,$user['secsalt'])===$user['secpassword'];
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
        if($type != 'number' && $i==0){
            $code .= $config['letter'][mt_rand(0, $strlen-8)];
        }else{
            $code .= $string[mt_rand(0, $strlen)];
        }
    }
    if (!empty($convert)) {
        $code = ($convert > 0) ? strtoupper($code) : strtolower($code);
    }
    return $code;
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


function format_date($date_str, $format){
    $time=strtotime($date_str);
    if($time>0){
        return date($format,$time);
    }
    return '';
}

function number_empty($val){
    $tval = floatval($val);
    return empty($tval)?'':$val;
}

/**
 * curl下载文件
 * @param mixed $durl 
 * @return string|bool 
 */
function curl_file_get_contents($durl, $timeout = 3, $referer = ''){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $durl);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.61 Safari/537.36');
    curl_setopt($ch, CURLOPT_REFERER,$referer);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $r = curl_exec($ch);
    curl_close($ch);
    return $r;
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
    if(stripos($url,"https://")!==FALSE){
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        //curl_setopt($ch, CURLOPT_SSLVERSION, 1);
    }
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


function gener_qrcode($text,$size=300,$margin=10,$errLevel='high'){
    $qrCode = new \Endroid\QrCode\QrCode();

    $qrCode->setText($text)
        ->setSize($size)
        ->setMargin($margin)
        ->setErrorCorrectionLevel($errLevel)
        ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
        ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
        //->setLabel('thinkphp.cn')
        //->setLabelFontSize(16)
        ->setWriterByName('png');
    return $qrCode->writeString();
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