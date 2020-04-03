<?php
/**
 * 模板配置
 * User: shirne
 * Date: 2018/5/2
 * Time: 20:48
 */

return [
    'static_version'=>'20181109',
    'view_path'=>app()->getRootPath().'/template/blog/',
    'independence'=>false,
    'taglib_pre_load'=>'app\common\taglib\Article,app\common\taglib\Product,app\common\taglib\Goods,app\common\taglib\Extendtag'
];