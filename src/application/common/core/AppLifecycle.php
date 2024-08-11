<?php

namespace app\common\core;

use think\facade\Log;

class AppLifecycle
{

    public function appInit()
    {
        Log::record('app_init');
        $dir = dirname(app()->getAppPath()) . '/extend/modules';
        $modules = scandir($dir);
        if (!empty($modules)) {
            foreach ($modules as $module) {
                if (file_exists($dir . '/' . $module . '/init.php')) {
                    @include_once($dir . '/' . $module . '/init.php');
                }
            }
        }
    }

    public function appBegin()
    {
        Log::record('app_begin');
    }

    public function moduleInit()
    {
        Log::record('module_init');
    }

    public function actionBegin()
    {
        Log::record('action_begin');
    }

    public function viewFilter()
    {
        Log::record('view_filter');
    }
    public function logWrite()
    {
        //Log::record('log_write');
    }
    public function appEnd()
    {
        Log::record('app_end');
    }
}
