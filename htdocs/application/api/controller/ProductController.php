<?php

namespace app\api\controller;

use app\common\facade\MemberFavouriteFacade;
use app\common\facade\ProductCategoryFacade;
use app\common\model\PostageModel;
use app\common\model\ProductModel;
use app\common\model\ProductSkuModel;
use think\Db;

/**
 * 产品操作接口
 * Class ProductController
 * @package app\api\Controller
 */
class ProductController extends BaseController
{
    public function get_all_cates(){
        return $this->response(ProductCategoryFacade::getTreedCategory());
    }

    public function get_cates($pid=0, $goods_count=0, $withsku=0){
        if($pid!=0 && preg_match('/^[a-zA-Z]\w+/',$pid)){
            $current=ProductCategoryFacade::findCategory($pid);
            if(empty($current)){
                return $this->response([]);
            }
            $pid=$current['id'];
        }
        $cates = ProductCategoryFacade::getSubCategory($pid);
        if($goods_count > 0){
            $product = ProductModel::getInstance();
            foreach($cates as &$cate){
                $cate['products']=$product->tagList([
                    'category'=>$cate['id'],
                    'recursive'=>1,
                    'limit'=>$goods_count,
                    'withsku'=>$withsku
                ]);
            }
            unset($cate);
        }
        return $this->response($cates);
    }

    public function get_list($cate='',$type='',$order='',$keyword='',$withsku=0,$page=1, $pagesize=10){
        $condition=[];
        if($cate){
            $condition['category']=$cate;
            $condition['recursive']=1;
        }
        if(!empty($order)){
            $condition['order']=$order;
        }
        if(!empty($keyword)){
            $condition['keyword']=$keyword;
        }
        if(!empty($type)){
            $condition['type']=$type;
        }
        if(!empty($withsku)){
            $condition['withsku']=$withsku;
        }
        $condition['page']=$page;
        $condition['pagesize']=$pagesize;
        
        $lists = ProductModel::getInstance()->tagList($condition, true);
        
        if(!empty($lists) && !$lists->isEmpty()) {
            $levels = getMemberLevels();
    
            $lists->each(function ($item) use ($levels) {
                if ($item['level_id']) {
                    $item['level_name'] = $levels[$item['level_id']]['level_name'] ?: '';
                }
        
                return $item;
            });
        }

        return $this->response([
            'lists'=>$lists->items(),
            'page'=>$lists->currentPage(),
            'total'=>$lists->total(),
            'total_page'=>$lists->lastPage(),
        ]);
    }

    public function view($id){
        $product = ProductModel::get($id);
        if(empty($product)){
            $this->error('商品不存在');
        }

        $skus=ProductSkuModel::where('product_id',$product['id'])->select();
        $images=Db::name('ProductImages')->where('product_id',$product['id'])->select();

        $isFavourite=$this->isLogin?MemberFavouriteFacade::isFavourite($this->user['id'],'product',$id):0;

        return $this->response([
            'product'=>$product,
            'is_favourite'=>$isFavourite,
            'postage'=>PostageModel::getDesc($product['postage_id']),
            'skus'=>$skus,
            'images'=>$images
        ]);
    }

    public function comments($id){
        $product = ProductModel::get($id);
        if(empty($product)){
            $this->error('参数错误');
        }
        $comments=Db::view('productComment','*')
            ->view('member',['username','realname','avatar'],'member.id=productComment.member_id','LEFT')
            ->where('product_id',$id)->paginate(10);

        return $this->response([
            'lists'=>$comments->items(),
            'page'=>$comments->currentPage(),
            'total'=>$comments->total(),
            'total_page'=>$comments->lastPage(),
        ]);
    }
}