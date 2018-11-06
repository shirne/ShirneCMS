<?php

namespace app\api\Controller;

use app\common\facade\ProductCategoryFacade;
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

    public function get_cates($pid=0){
        if($pid!=0 && preg_match('/^[a-zA-Z]\w+/',$pid)){
            $current=ProductCategoryFacade::findCategory($pid);
            if(empty($current)){
                $this->response([]);
            }
            $pid=$current['id'];
        }
        return $this->response(ProductCategoryFacade::getSubCategory($pid));
    }

    public function get_list($cate='',$page=1){
        $model=Db::view('product','*')
            ->view('productCategory',['name'=>'category_name','title'=>'category_title'],'product.cate_id=productCategory.id','LEFT');

        $model->where('article.status',1);
        if($cate){
            $category=ProductCategoryFacade::findCategory($cate);
            $model->whereIn('product.cate_id',ProductCategoryFacade::getSubCateIds($category['id']));
        }
        $lists = $model->paginate(10);
        $lists->each(function($item){
            if(!empty($item['prop_data'])){
                $item['prop_data']=json_decode($item['prop_data'],true);
            }
            $item['prop_data']=[];
            return $item;
        });

        return $this->response([
            'lists'=>$lists->items(),
            'page'=>$lists->currentPage(),
            'count'=>$lists->total(),
            'total_page'=>$lists->lastPage(),
        ]);
    }

    public function view($id){
        $product = ProductModel::get($id);
        if(empty($product)){
            $this->error('商品不存在');
        }

        $skuModel=new ProductSkuModel();

        $skus=$skuModel->where('product_id',$product['id'])->select();
        $images=Db::name('ProductImages')->where('product_id',$product['id'])->select();


        return $this->response([
            'product'=>$product,
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
            'count'=>$comments->total(),
            'total_page'=>$comments->lastPage(),
        ]);
    }

    public function do_comment(){

    }
}