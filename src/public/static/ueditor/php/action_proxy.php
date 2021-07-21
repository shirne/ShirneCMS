<?php
/**
 * 代理远程图片
 * User: Shirne
 * Date: 2019-07-23
 * Time: 早上07:29
 */
!defined('IN_CONTROLLER') && exit;
set_time_limit(0);

$fieldName = $CONFIG['proxyFieldName']?:'remote';

/* 抓取远程图片 */
if (isset($_POST[$fieldName])) {
    $source = $_POST[$fieldName];
} else {
    $source = $_GET[$fieldName];
}

//来源
$refurl = '';
if (isset($_POST['referer'])) {
    $refurl = $_POST['referer'];
} else {
    $refurl = $_GET['referer'];
}
//压缩
$maxwidth=0;
if (isset($_POST['maxwidth'])) {
    $maxwidth = $_POST['maxwidth'];
} else {
    $maxwidth = $_GET['maxwidth'];
}
$maxwidth = intval($maxwidth);


$urls = parse_url(strtolower($source));
if(empty($urls['scheme'])){
    $scheme = 'http';
    $source = $scheme.'://'.ltrim($source,'\t\n\r\0\x0B:/');
}else{
    $scheme = $urls['scheme'];
}
if(in_array($scheme,['http','https','ftp'])){
    if(empty($refurl)){
        
        if($urls){
            $refurl = $scheme.'://'.$urls['host '];
            if($scheme == 'http' && (!empty($urls['port']) && $urls['port']!='80')){
                $refurl .= ':'.$urls['port'];
            }
            if($scheme == 'https' && (!empty($urls['port']) && $urls['port']!='443')){
                $refurl .= ':'.$urls['port'];
            }
            $refurl .= '/';
        }
    }
    $context=stream_context_create([
        $scheme=>array(
            'method'=>"GET",
            'header'=>"User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.142 Safari/537.36\r\n".
                "Referer: $refurl\r\n"
        )
    ]);


    $data = file_get_contents($source,false, $context);
    if(strlen($data)< 50 && $scheme == 'http'){
        $scheme = 'https';
        $context=stream_context_create([
            $scheme=>array(
                'method'=>"GET",
                'header'=>"User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.142 Safari/537.36\r\n".
                    "Referer: $refurl\r\n"
            )
        ]);
        $data = file_get_contents($source,false, $context);
    }

    if(strlen($data) > 100){
        header("Content-type: image/jpeg");
        if($maxwidth > 0){
            $image = imagecreatefromstring($data);
            if($image){
                $width = imagesx($image);
                $height = imagesy($image);

                $sw=0;
                if($width > $height){
                    if($width > $maxwidth){
                        $sw = $maxwidth;
                        $sh = $height * $sw / $width;
                    }
                }else{
                    if($height > $maxwidth){
                        $sh = $maxwidth;
                        $sw = $width * $sh / $height;
                    }
                }
                if($sw > 0){
                    $newimage = imagecreatetruecolor($sw,$sh);
                    imagecopyresampled($newimage, $image, 0, 0, 0, 0, $sw, $sh, $width, $height);
                    
                    imagejpeg($newimage,null,70);
                    imagedestroy($newimage);
                }else{
                    imagejpeg($image,null,70);
                }
                imagedestroy($image);
            }
        }else{
            echo $data;
        }
    }
}
header("Content-type: image/png");
echo base64_decode('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');


exit;