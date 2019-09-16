<?php

namespace app\api\processer;

use app\common\model\ArticleModel;
use app\common\model\ProductModel;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use think\Db;

class SystemProcesser extends BaseProcesser
{
    public function _getActions()
    {
        return [
            'processer'=>'system',
            'actions'=>[
                'getNewArticles'=>[],
                'getNewProducts'=>[]
            ]
        ];
    }

    public function process($args){
        switch ($args['action']){
            case 'getNewArticles':
                return $this->getNewArticles($args);
                break;
            case 'getNewProducts':
                return $this->getNewProducts($args);
                break;
            default:
                return "";
        }
    }

    private function getNewArticles($args)
    {
        $lists = ArticleModel::getInstance()->tagList($args);
        $items = [];
        foreach ($lists as $item) {
            $items[]=new NewsItem([
                'title'=>$item['title'],
                'description'=>$item['description'],
                'url'=>url('index/article/view',['id'=>$item['id']], true, true),
                'image'=>local_media($item['cover'])
            ]);
        }
        return new News($items);
    }

    private function getNewProducts($args)
    {
        $lists = ProductModel::getInstance()->tagList($args);
        $items = [];
        foreach ($lists as $item) {
            $items[]=new NewsItem([
                'title'=>$item['title'],
                'description'=>$item['vice_title'],
                'url'=>url('index/product/view',['id'=>$item['id']], true, true),
                'image'=>local_media($item['image'])
            ]);
        }
        return new News($items);
    }
}