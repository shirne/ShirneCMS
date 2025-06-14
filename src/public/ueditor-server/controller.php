<?php
//header('Access-Control-Allow-Origin: http://www.baidu.com'); //设置http://www.baidu.com允许跨域访问
//header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With'); //设置允许的跨域header
date_default_timezone_set("Asia/chongqing");
error_reporting(E_ERROR);
header("Content-Type: text/html; charset=utf-8");

define('IN_CONTROLLER', 1);

$CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents("config.json")), true);
$action = $_GET['action'];

$sessconfig = include(__DIR__ . '/../../config/session.php');

session_start();

$sesskey = $sessconfig['prefix'];
$sess = empty($_SESSION[$sesskey]) ? [] : $_SESSION[$sesskey];
$userid = isset($sess['userid']) ? $sess['userid'] : 0;
$adminid = isset($sess['adminId']) ? $sess['adminId'] : 0;

session_write_close();

$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

$isAdmin = false;
if (!empty($referer)) {
    $part = parse_url($referer);
    if (strpos($part['path'], '/admin/') === 0) {
        if (empty($adminid)) {
            echo json_encode(array(
                'state' => '无权上传文件'
            ));
            exit;
        }
        $isAdmin = true;
    }
}
if (!$isAdmin) {
    if (empty($userid)) {
        echo json_encode(array(
            'state' => '请登录后再上传文件'
        ));
        exit;
    }
}

switch ($action) {
    case 'config':
        $result =  json_encode($CONFIG);
        break;

    /* 上传图片 */
    case 'uploadimage':
        /* 上传涂鸦 */
    case 'uploadscrawl':
        /* 上传视频 */
    case 'uploadvideo':
        /* 上传文件 */
    case 'uploadfile':
        $result = include("action_upload.php");
        break;

    /* 列出图片 */
    case 'listimage':
        $result = include("action_list.php");
        break;
    /* 列出文件 */
    case 'listfile':
        $result = include("action_list.php");
        break;

    /* 抓取远程文件 */
    case 'catchimage':
        $result = include("action_crawler.php");
        break;
    case 'proxy':
        $result = include("action_proxy.php");
        break;
    default:
        $result = json_encode(array(
            'state' => '请求地址出错'
        ));
        break;
}

/* 输出结果 */
if (isset($_GET["callback"])) {
    if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
        echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
    } else {
        echo json_encode(array(
            'state' => 'callback参数不合法'
        ));
    }
} else {
    echo $result;
}
