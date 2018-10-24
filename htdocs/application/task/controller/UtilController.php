<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/4/20
 * Time: 23:50
 */

namespace app\task\controller;


use app\common\command\Install;
use think\console\Input;
use think\console\Output;
use think\Controller;

class UtilController extends Controller
{
    public function cropimage(){
        crop_image($_GET['img'],$_GET);
        exit;
    }

    public function install(){
        $install=new Install();
        $output=new Output('buffer');
        $input=new Input();

        $install->run($input, $output);
        return $output->fetch();
    }

    public function test(){
        $im = imagecreatetruecolor(120, 20);
        $text_color = imagecolorallocate($im, 233, 14, 91);
        imagestring($im, 1, 5, 5,  'A Simple Text String', $text_color);

// 设置内容类型标头 —— 这个例子里是 image/jpeg
        header('Content-Type: image/jpeg');

// 使用 NULL 跳过 filename 参数，并设置图像质量为 75%
        imagejpeg($im, NULL, 75);

// 释放内存
        imagedestroy($im);
        exit;
    }
}