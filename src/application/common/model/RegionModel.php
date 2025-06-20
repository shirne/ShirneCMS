<?php

namespace app\common\model;


use think\Db;
use think\facade\Lang;

/**
 * Class RegionModel
 * @package app\common\model
 */
class RegionModel extends CategoryModel
{
    protected $precache = 'region_';

    protected function _get_data()
    {
        return Db::name('Region')->order('pid ASC,sort ASC,id ASC')->select();
    }

    public static function GetTitles($keys, $lang = '')
    {
        if (empty($lang)) {
            $lang = Lang::range();
        }
        if ($keys === '0') {
            return [lang('Global')];
        }
        $returns = [];
        if (!empty($keys)) {
            $isEn = strpos($lang, 'en') !== false;
            $instance = static::getInstance();
            if (!is_array($keys)) $keys = explode(',', $keys);
            foreach ($keys as $key) {
                $cate = $instance->findCategory($key);
                if (!empty($cate)) {
                    $returns[] = $cate[$isEn ? 'title_en' : 'title'];
                }
            }
        }

        return $returns;
    }

    public static function GetCodes()
    {
        if (empty($lang)) {
            $lang = Lang::range();
        }
        $isEn = strpos($lang, 'en') !== false;
        $data = Db::name('region')->where('code', '<>', '')->order('sort asc')->select();

        return $data;
    }
}
