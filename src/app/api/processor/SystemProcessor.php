<?php

namespace app\api\processor;

use app\common\model\ArticleModel;
use app\common\model\MemberModel;
use app\common\model\ProductModel;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Text;

class SystemProcessor extends BaseProcessor
{
    public function _getActions()
    {
        return [
            'processor'=>'system',
            'actions'=>[
                'getNewArticles'=>[],
                'getNewProducts'=>[],
                'getPoster'=>[]
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
            case 'getPoster':
                return $this->getPoster($args);
                break;
            default:
                return "";
        }
    }

    protected function getNewArticles($args)
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

    protected function getNewProducts($args)
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

    protected function getPoster($args)
    {
        $member = $this->getMember();
        if(!$member || $member['is_agent']<1){
            return new Text('您还不是代理，请升级后再分享');
        }
        if(empty($member['agentcode'])){
            return new Text('代理信息异常');
        }
        $cashkey = 'poster-'.random_str(3).'-'.time();
        cache($cashkey,$member['id'].'-'.$this->handler->account_id);
        $url = url('task/util/poster',['key'=>$cashkey],true,true);

        $ch = curl_init();
        $headers = array("Content-type: application/json;charset='utf-8'",
            "Accept: application/json",
            "Cache-Control: no-cache","Pragma: no-cache");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
        //curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER , 1 );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch,CURLOPT_TIMEOUT,1);
        //curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
        //curl_setopt($ch, CURLOPT_TIMEOUT_MS, 200);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_NOBODY, 0);
        $result = curl_exec($ch);
        curl_close($ch);

        return "";
    }
}