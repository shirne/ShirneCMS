<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用行为扩展定义文件

use app\common\core\AppLifecycle;

return [
    // 应用初始化
    'app_init'     => [
        AppLifecycle::class
    ],
    // 应用开始
    'app_begin'    => [
        AppLifecycle::class
    ],
    // 模块初始化
    'module_init'  => [
        AppLifecycle::class
    ],
    // 操作开始执行
    'action_begin' => [
        AppLifecycle::class
    ],
    // 视图内容过滤
    'view_filter'  => [
        AppLifecycle::class
    ],
    // 日志写入
    'log_write'    => [
        AppLifecycle::class
    ],
    // 应用结束
    'app_end'      => [
        AppLifecycle::class
    ],
];
