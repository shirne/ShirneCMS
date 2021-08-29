# 静态资源分离的阿里云oss设置方案

## 配置
upload.php 驱动模式设置为 mirror
```
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
```

## 阿里云OSS
* 创建一个bucket
* 基础设置 => 镜像回源 设置需要回源的地址

## PC/H5端
* 静态资源调用
```
// config/template.php
'tpl_replace_string'  =>  [
    '__STATIC__'=>'bucket地址/static/'
]
```
* 上传文件调用
```
{$article.cover|media}
```

## Vue/小程序/APP 端

配置图片前缀为bucket地址
```
// vue config.js
imgServer : 'bucket地址/static/'


// 小程序 app.js
globalData.imgDir 

// APP lib/config.dart
Config.imgHost

```
