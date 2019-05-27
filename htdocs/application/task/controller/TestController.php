<?php

namespace app\task\controller;

use shirne\common\Image;


/**
 * 测试功能
 * Class TestController
 * @package app\task\controller
 */
class TestController
{
    public function image()
    {
        $url = './uploads/article/2019/01/7c9028a9010bbc7cb84056b2ebdfd706.png';
        $image = new Image($url);
        $image->crop(0,0,100,100);
        $image->output();
        exit;
    }
}