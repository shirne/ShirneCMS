<?php

namespace app\common\facade;

use app\common\core\SimpleFacade;

/**
 * 表字段翻译
 * Class TranslateFacade
 * @package app\common\facade
 * @method array loadAll() static 加载所有语言的翻译数据
 * @method array loadLang($lang) static 加载指定语言的翻译数据
 * @method array loadTable($table,$lang='') static 加载指定表的翻译数据，默认加载当前语言
 * @method array|string get_trans($table,$key='',$field='',$lang='') static 获取指定翻译数据
 * @method array trans_list($data,$table,$key_id='id',$lang='') static 翻译数据列表
 */
class TranslateFacade extends SimpleFacade
{
    protected static function getFacadeClass(){
        return \app\common\model\TranslateModel::class;
    }
}