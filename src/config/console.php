<?php
// +----------------------------------------------------------------------
// | 控制台配置
// +----------------------------------------------------------------------
return [
    // 指令定义
    'commands' => [
        'install'=>'app\common\command\Install',
        'testing'=>'app\common\command\Testing',
        'manager'=>'app\common\command\Manager',
        'bootstrap'=>'app\common\command\Bootstrap',
    ],
];
