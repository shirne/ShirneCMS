<?php

namespace app\common\facade;

use app\common\core\SimpleFacade;

/**
 * 表字段翻译
 * Class TranslateFacade
 * @package app\common\facade
 * @method static array loadAll() 加载所有语言的翻译数据
 * @method static array loadLang($lang) 加载指定语言的翻译数据
 * @method static array loadTable($table,$lang='') 加载指定表的翻译数据，默认加载当前语言
 * @method static array|string get_trans($table,$key='',$field='',$lang='') 获取指定翻译数据
 * @method static array trans_list($data,$table,$key_id='id',$lang='') 翻译数据列表
 */
class TranslateFacade extends SimpleFacade
{
    protected static function getFacadeClass(){
        return \app\common\model\TranslateModel::class;
    }
}