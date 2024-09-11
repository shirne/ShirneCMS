<?php

namespace app\common\core;

use think\facade\Log;

class AppLifecycle
{

    protected static $events = [];
    public static function addEvent($event, $callback)
    {
        if (!isset(self::$events[$event])) {
            self::$events[$event] = [];
        }
        self::$events[$event][] = $callback;
    }

    public static function triggerEvent($event, ...$args)
    {
        if (!empty(self::$events[$event])) {
            foreach (self::$events[$event] as $callback) {
                try {
                    if (empty($args)) $args = [];
                    call_user_func_array($callback, $args);
                } catch (\Exception $e) {
                    Log::warning("Event call error: $event" . var_export($callback) . "\n $e");
                }
            }
        }
    }

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
        static::triggerEvent('app_begin');
    }

    public function moduleInit()
    {
        Log::record('module_init');
        static::triggerEvent('module_init');
    }

    public function actionBegin()
    {
        Log::record('action_begin');
        static::triggerEvent('action_begin');
    }

    public function viewFilter()
    {
        Log::record('view_filter');
        static::triggerEvent('view_filter');
    }
    public function logWrite()
    {
        //Log::record('log_write');
    }
    public function appEnd()
    {
        Log::record('app_end');
        static::triggerEvent('app_end');
    }
}
