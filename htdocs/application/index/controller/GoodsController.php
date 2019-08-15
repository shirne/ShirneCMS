<?php

namespace app\index\controller;

use app\common\model\GoodsModel;
use app\common\facade\GoodsCategoryFacade;
use think\Db;

class GoodsController extends BaseController
{
    protected $categries;
    protected $category;
    protected $categoryTree;
    protected $pagesize=12;

    public function initialize()
    {
        parent::initialize();
        $this->assign('navmodel','goods');
    }

    public function index($name=""){
        $this->category($name);
        $model=Db::view('goods','*')
            ->view('goodsCategory',['name'=>'category_name','title'=>'category_title'],'goods.cate_id=goodsCategory.id','LEFT')->where('status',1);

        if(!empty($this->category)){
            $this->seo($this->category['title']);
            $model->whereIn('goods.cate_id',GoodsCategoryFacade::getSubCateIds($this->category['id']));

        }else{
            $this->seo("积分商城");
        }

        $model=$model->order('goods.sort DESC,goods.id DESC')->paginate($this->pagesize);

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
        $goods = GoodsModel::get($id);
        if(empty($goods)){
            $this->error('商品不存在');
        }
        $this->seo($goods['title']);
        $this->category($goods['cate_id']);

        $this->assign('goods', $goods);

        $this->assign('images',Db::name('GoodsImages')->where('goods_id',$goods['id'])->select());
        return $this->fetch();
    }

    private function category($name=''){

        $this->category=GoodsCategoryFacade::findCategory($name);
        $this->categoryTree=GoodsCategoryFacade::getCategoryTree($name);
        $this->categries=GoodsCategoryFacade::getTreedCategory();
        if(empty($this->category)){
            $this->category=['id'=>0,'title'=>'积分商城'];
        }


        $this->assign('category',$this->category);
        $this->assign('categoryTree',$this->categoryTree);
        $this->assign('categories',$this->categries);

        if(!empty($this->categoryTree)) {
            $this->assign('navmodel', 'goods-' . $this->categoryTree[0]['name']);
        }
    }
}