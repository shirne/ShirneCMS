<?php

namespace app\index\controller;

use app\common\model\PostageModel;
use app\common\model\ProductModel;
use app\common\facade\ProductCategoryFacade;
use app\common\model\MemberFavouriteModel;
use app\common\model\ProductCommentModel;
use app\common\model\ProductSkuModel;
use app\common\validate\ProductCommentValidate;
use think\Db;

/**
 * 产品控制器
 * Class ProductController
 * @package app\index\controller
 */
class ProductController extends BaseController
{
    protected $categries;
    protected $category;
    protected $categoryTree;
    protected $pagesize=12;

    public function initialize()
    {
        parent::initialize();
        $this->assign('navmodel','product');
        $this->seo($this->config['shop_pagetitle'],$this->config['shop_keyword'],$this->config['shop_description']);
    }

    public function index($name=""){
        $this->category($name);

        $model=Db::view('product','*')
            ->view('productCategory',
                ['name'=>'category_name','title'=>'category_title'],
                'product.cate_id=productCategory.id',
                'LEFT');

        if(!empty($this->category)){
            $this->seo($this->category['title'],$this->category['keywords'],$this->category['description']);
            $model->whereIn('product.cate_id',ProductCategoryFacade::getSubCateIds($this->category['id']));
        }

        $lists=$model->where('product.status',1)
            ->order('product.create_time DESC,product.id DESC')
            ->paginate($this->pagesize);

        $lists->each(function($item){
            if(!empty($item['prop_data'])){
                $item['prop_data']=force_json_decode($item['prop_data'],true);
            }
            return $item;
        });

        $this->assign('lists', $lists);
        $this->assign('page',$lists->render());

        return $this->fetch();
    }

    public function view($id){
        $product = ProductModel::get($id);
        if(empty($product)){
            return $this->errorPage('商品不存在');
        }
        $product['sale']+=$product['v_sale'];
        $this->seo($product['title']);
        $this->category($product['cate_id']);

        $this->assign('product', $product);
        $this->assign('postage',PostageModel::getDesc($product['postage_id']));
        $this->assign('skus', ProductSkuModel::where('product_id',$product['id'])->select());
        $this->assign('images',Db::name('ProductImages')->where('product_id',$product['id'])->select());
        $this->assign('isFavourite',(new MemberFavouriteModel())->isFavourite($this->userid, 'product', $id));
        return $this->fetch();
    }

    public function favourite($id, $cancel = 0){
        if(!$this->isLogin){
            $this->error('请先登录');
        }
        $model=new MemberFavouriteModel();
        if($cancel){
            if($model->removeFavourite($this->user['id'],'product',$id)){
                $this->success('已取消收藏');
            }else{
                $this->error('未收藏该产品');
            }
        }elseif($model->addFavourite($this->user['id'],'product',$id)){
            $this->success('已添加收藏');
        }
        $this->error($model->getError());
    }

    public function flash($id, $date){
        $flash = ProductModel::getFlash($id,$date);
        if(empty($flash)){
            return $this->errorPage('商品快照不存在');
        }
        $product = json_decode($flash['product'],true);

        $product['sale']+=$product['v_sale'];
        $this->seo($product['title']);
        $this->category($product['cate_id']);

        $images= json_decode($flash['images'],true);
        if(empty($images)){
            $images = [
                ['image'=>$product['image']]
            ];
        }
        $this->assign('product', $product);
        $this->assign('brand', json_decode($flash['brand'],true));
        $this->assign('skus', json_decode($flash['images'],true));
        $this->assign('images',$images);
        $this->assign('isFlash',1);
        $this->assign('flashDate',$flash['timestamp']);
        return $this->fetch('product/view');
    }
    
    public function comment($id){
        $product = ProductModel::get($id);
        if(empty($product)){
            $this->error('参数错误');
        }
        if($this->request->isPost()){
            $data=$this->request->only('product_id,email,is_anonymous,content','POST');
            $validate=new ProductCommentValidate();
            if(!$validate->check($data)){
                $this->error($validate->getError());
            }else{
                $data['member_id']=$this->userid;
                if(empty($data['member_id'])){
                    redirect()->remember();
                    $this->error('请先登录',url('index/login/index'));
                }
                $model=ProductCommentModel::create($data);
                if($model['id']){
                    $this->success('评论成功');
                }else{
                    $this->error('评论失败');
                }
            }
        }
        $comments=Db::view('productComment','*')
            ->view('member',['username','realname','avatar'],'member.id=productComment.member_id','LEFT')
            ->where('productComment.status',1)
            ->where('product_id',$id)
            ->order('productComment.create_time desc')->paginate(10);
        
        if($this->request->isAjax()){
            $this->success('','',[
                'comments'=>$comments->items(),
                'page'=>$comments->currentPage(),
                'total'=>$comments->total(),
                'total_page'=>$comments->lastPage(),
            ]);
        }
            
        $this->seo($product['title']);
        $this->category($product['cate_id']);

        $this->assign('product',$product);
        $this->assign('comments',$comments);
        $this->assign('page',$comments->render());
        return $this->fetch();
    }

    private function category($name=''){

        $this->category=ProductCategoryFacade::findCategory($name);
        $this->categoryTree=ProductCategoryFacade::getCategoryTree($name);
        $this->categries=ProductCategoryFacade::getTreedCategory();
        if(empty($this->category)){
            $this->category=['id'=>0,'title'=>'产品中心'];
        }


        $this->assign('category',$this->category);
        $this->assign('categoryTree',$this->categoryTree);
        $this->assign('categories',$this->categries);

        if(!empty($this->categoryTree)) {
            $this->assign('navmodel', 'product-' . $this->categoryTree[0]['name']);
        }
    }
}