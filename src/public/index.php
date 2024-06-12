<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]
namespace think;

define('DOC_ROOT', __DIR__);

// 加载基础文件
require __DIR__ . '/../thinkphp/base.php';

// 忽略 array key 异常
set_error_handler(function (int $code, string $message, string $file = '', int $line = 0, ?array $context = []) {
    if (strpos($message, 'Undefined array key') == 0) {
        Log::error($code . ':' . $message . "\n$file [$line]");
        return true;
    }
    return false;
}, E_ALL);

// 支持事先使用静态方法设置Request对象和Config对象

// 执行应用并响应
Container::get('app')->run()->send();
