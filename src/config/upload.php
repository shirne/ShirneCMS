<?php
// +----------------------------------------------------------------------
// | 上传设置
// +----------------------------------------------------------------------

return [
    // 驱动方式
    'driver' => 'local',
    // 根目录
    'root_path'   => './uploads/',
    // 目录格式
    'save_path'   => 'Y/m/',

    'default_img' => './static/images/blank.gif',

    'default_size' => 300,

    'default_quality' => 80,

    'driver_mode'=>'mirror',
    'driver_config'=>[
        'driver' => 'oss',
        'access_id' => '',
        'secret_key' => '',
        'bucket' => '',
        'domain' => '',
        'url' => ''
    ]
];
//oss driverConfig
/*
'driver' => 'oss',
'access_id' => '',
'secret_key' => '',
'bucket' => '',
'domain' => '',
'url' => '',
*/

//qiniu driverConfig
/*
'access_key' => '',
'secret_key' => '',
'bucket' => '',
'domain' => '',
'url' => '',
'driver' => 'qiniu',
*/