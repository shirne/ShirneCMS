<?php
/**
 * 模板配置
 * User: shirne
 * Date: 2018/5/2
 * Time: 20:48
 */

return [
    'view_path'=>Env::get('root_path').'template'.DIRECTORY_SEPARATOR,
    'independence'=>true,
    'taglib_pre_load'=>'app\common\taglib\Article,app\common\taglib\Product,app\common\taglib\Extend'
];