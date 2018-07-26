<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/4/20
 * Time: 23:50
 */

namespace app\task\controller;


use app\common\command\Install;
use think\Controller;

class UtilController extends Controller
{
    public function cropimage(){
        crop_image($_GET['img'],$_GET);
        exit;
    }

    public function install(){
        $install=new Install();
        $install->run();
    }
}