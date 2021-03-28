<?php


namespace app\common\model;


use app\common\core\BaseModel;
use app\common\facade\CategoryFacade;
use app\common\facade\ProductCategoryFacade;
use think\Db;

class BoothModel extends BaseModel
{
    protected $autoWriteTimestamp = true;
    protected $type = ['data'=>'array'];
    
    public static function fetchBooth($flags,$single=false){
        if(!is_array($flags))$flags=explode(',',$flags);
        
        $booths = static::whereIn('flag',$flags)->where('status',1)->select();
        $lists=[];
        foreach ($booths as $booth){
            $lists[$booth['flag']]=$booth->fetchData();
        }
        if($single){
            return isset($lists[$flags[0]])?$lists[$flags[0]]:[];
        }
        return $lists;
    }
    
    protected $listData=null;
    public function fetchData(){
        if(is_null($this->listData)){
            $this->listData=[];
            $method='fetch_'.$this['type'];
            if(!empty($this['data']) && method_exists($this,$method)){
                $this->listData=call_user_func([$this,$method],$this['data']);
            }
        }
        return $this->listData;
    }
    
    private function fetch_category($args){
        //手动选择
        if($args['type'] == '1'){
            $list = [];
            if(!empty($args['category_ids'])) {
                $ids = idArr($args['category_ids']);
                foreach ($ids as $id){
                    $list[]= CategoryFacade::findCategory($id);
                }
            }
        }else{
            $list = CategoryFacade::getSubCategory($args['parent_id']);
            $count=isset($args['count'])?intval($args['count']):0;
            if($count>0 && count($list)>$count){
                array_splice($list,$count);
            }
        }
        $article_count = isset($args['article_count'])?intval($args['article_count']):0;
        if($article_count > 0){
            $article = ArticleModel::getInstance();
            $filters['limit']=$article_count;
            $filters['recursive']=1;
            if(!empty($args['article_sort'])){
                $filters['order']=$args['article_sort'];
            }

            foreach($list as &$cate){
                $filters['category']=$cate['id'];
                $cate['articles']=$article->tagList($filters);
            }
            unset($cate);
        }
        return $list;
    }
    private function fetch_article($args){
        $list=[];
        if($args['type'] == '1'){
            if(!empty($args['article_ids'])) {
                $list = ArticleModel::getInstance()->tagList([
                    'ids' => $args['article_ids']
                ]);
            }
        }else{
            $tagargs=[];
            if(!empty($args['category_id'])){
                $tagargs['category']=$args['category_id'];
                $tagargs['recursive']=1;
            }
            if(!empty($args['filter_type'])){
                $tagargs['type']=$args['filter_type'];
            }
            if(!empty($args['count'])){
                $tagargs['limit']=$args['count'];
            }
            $list = ArticleModel::getInstance()->tagList($tagargs);
        }
        return $list;
    }
    private function fetch_product_category($args){
        if($args['type'] == '1'){
            $list = [];
            if(!empty($args['product_category_ids'])) {
                $ids = idArr($args['product_category_ids']);
                foreach ($ids as $id){
                    $list[]= ProductCategoryFacade::findCategory($id);
                }
            }
        }else{
            $list = ProductCategoryFacade::getSubCategory($args['parent_id']);
            $count=isset($args['count'])?intval($args['count']):0;
            if($count>0 && count($list)>$count){
                array_splice($list,$count);
            }
        }
        $goods_count = isset($args['product_count'])?intval($args['product_count']):0;
        if($goods_count > 0){
            $product = ProductModel::getInstance();
            $filters['limit']=$goods_count;
            $filters['withsku']=1;
            $filters['recursive']=1;
            if(!empty($args['product_sort'])){
                $filters['order']=$args['product_sort'];
            }

            foreach($list as &$cate){
                $filters['category']=$cate['id'];
                $cate['products']=$product->tagList($filters);
            }
            unset($cate);
        }
        return $list;
    }
    private function fetch_product($args){
        $list=[];
        if($args['type'] == '1'){
            if(!empty($args['product_ids'])) {
                $list = ProductModel::getInstance()->tagList([
                    'ids' => $args['product_ids']
                ]);
            }
        }else{
            $tagargs=[];
            if(!empty($args['category_id'])){
                $tagargs['category']=$args['category_id'];
                $tagargs['recursive']=1;
            }
            if(!empty($args['filter_type'])){
                $tagargs['type']=$args['filter_type'];
            }
            if(!empty($args['filter_price'])){
                $tagargs['price']=$args['filter_price'];
            }
            if(!empty($args['count'])){
                $tagargs['limit']=$args['count'];
            }
            $list = ProductModel::getInstance()->tagList($tagargs);
        }
        return $list;
    }
    private function fetch_ad($args){
        return AdvGroupModel::getAdList($args['ad_flag'],empty($args['count'])?10:intval($args['count']));
    }
}