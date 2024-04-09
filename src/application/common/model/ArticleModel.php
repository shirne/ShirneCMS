<?php

namespace app\common\model;

use app\common\core\ContentModel;
use app\common\facade\CategoryFacade;
use think\Db;
use think\Paginator;

/**
 * Class ArticleModel
 * @package app\common\model
 */
class ArticleModel extends ContentModel
{
    protected $autoWriteTimestamp = true;
    protected $type = ['prop_data' => 'array'];

    function __construct($data = [])
    {
        parent::__construct($data);
        $this->cateFacade = CategoryFacade::getFacadeInstance();
        $this->searchFields = 'title|vice_title|description';
    }

    /**
     * @param array|Paginator $lists
     * @param array $attrs
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function afterTagList($lists, $attrs)
    {
        if (!empty($lists)) {
            $pids = array_column(is_array($lists) ? $lists : $lists->items(), 'id');
            if (!empty($attrs['withimgs'])) {
                $imgs = Db::name('articleImages')->whereIn('article_id', $pids)->select();
                $imgs = array_index($imgs, 'article_id', true);
                $lists = $this->appendTagData($lists, 'imgs', $imgs);
            }
        }
        return $lists;
    }

    protected function afterTagItem($item, $attrs = [])
    {
        $item['digg'] = $item['digg'] + intval($item['v_digg']);
        $item['views'] = $item['views'] + intval($item['v_views']);
        return $item;
    }
}
