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
    public function cropimage($img){
        return crop_image($img,$_GET);
    }

    public function cacheimage($img){
        $paths=explode('.',$img);
        if(count($paths)==3) {
            preg_match_all('/(w|h|q|m)(\d+)(?:_|$)/', $paths[1], $matches);
            $args = [];
            foreach ($matches[1] as $idx=>$key){
                $args[$key]=$matches[2][$idx];
            }
            $response = crop_image($paths[0].'.'.$paths[2], $args);
            if($response->getCode()==200) {
                file_put_contents(DOC_ROOT . '/' . $img, $response->getData());
            }
            return $response;
        }else{
            return redirect(ltrim(config('upload.default_img'),'.'));
        }
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