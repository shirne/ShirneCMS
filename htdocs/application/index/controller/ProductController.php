<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/5/13
 * Time: 9:55
 */

namespace app\index\controller;

use app\common\model\ProductModel;
use app\common\facade\ProductCategoryFacade;
use app\common\model\ProductCommentModel;
use app\common\model\ProductSkuModel;
use app\common\validate\ProductCommentValidate;
use think\Db;

class ProductController extends BaseController
{
    protected $categries;
    protected $category;
    protected $categoryTree;

    public function initialize()
    {
        parent::initialize();
        $this->assign('navmodel','product');
    }

    public function index($name=""){
        $this->category($name);

        $where=array();
        if(!empty($this->category)){
            $this->seo($this->category['title']);
            $where[]=array('product.cate_id','in',ProductCategoryFacade::getSubCateIds($this->category['id']));
        }else{
            $this->seo("产品中心");
        }

        $model=Db::view('product','*')
            ->view('productCategory',['name'=>'category_name','title'=>'category_title'],'product.cate_id=productCategory.id','LEFT')
            ->where($where)
            ->paginate(10);

        $model->each(function($item){
            if(!empty($item['prop_data'])){
                $item['prop_data']=json_decode($item['prop_data'],true);
            }
            $item['prop_data']=[];
            return $item;
        });

        $this->assign('lists', $model);
        $this->assign('page',$model->render());

        return $this->fetch();
    }

    public function view($id){
        $product = ProductModel::get($id);
        if(empty($product)){
            $this->error('商品不存在');
        }
        $this->seo($product['title']);
        $this->category($product['cate_id']);

        $this->assign('product', $product);
        $skuModel=new ProductSkuModel();
        $this->assign('skus', $skuModel->where('product_id',$product['id'])->select());
        $this->assign('images',Db::name('ProductImages')->where('product_id',$product['id'])->select());
        return $this->fetch();
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
        $this->seo($product['title']);
        $this->category($product['cate_id']);
        $comments=Db::view('productComment','*')
            ->view('member',['username','realname'],'member.id=articleComment.member_id','LEFT')
            ->where('product_id',$id)->paginate(10);

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