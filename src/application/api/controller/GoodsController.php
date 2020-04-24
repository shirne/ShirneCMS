<?php

namespace app\api\controller;

use app\common\facade\GoodsCategoryFacade;
use app\common\model\GoodsModel;
use think\Db;

/**
 * 产品操作接口
 * Class ProductController
 * @package app\api\Controller
 */
class GoodsController extends BaseController
{
    public function get_all_cates(){
        return $this->response(GoodsCategoryFacade::getTreedCategory());
    }

    public function get_cates($pid=0, $goods_count=0, $filters=[]){
        if($pid!=0 && preg_match('/^[a-zA-Z]\w+/',$pid)){
            $current=GoodsCategoryFacade::findCategory($pid);
            if(empty($current)){
                return $this->response([]);
            }
            $pid=$current['id'];
        }
        $cates = GoodsCategoryFacade::getSubCategory($pid);
        if($goods_count > 0){
            $goods = GoodsModel::getInstance();
            $filters['limit']=$goods_count;
            if(!isset($filters['recursive'])){
                $filters['recursive']=1;
            }
            foreach($cates as &$cate){
                $filters['category']=$cate['id'];
                $cate['goods']=$goods->tagList($filters);
            }
            unset($cate);
        }
        return $this->response($cates);
    }

    public function get_list($cate='',$order='',$keyword='',$page=1, $pagesize=10){
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
        $condition['page']=$page;
        $condition['pagesize']=$pagesize;
        
        $lists = GoodsModel::getInstance()->tagList($condition, true);

        return $this->response([
            'lists'=>$lists->items(),
            'page'=>$lists->currentPage(),
            'count'=>$lists->total(),
            'total_page'=>$lists->lastPage(),
        ]);
    }

    public function view($id){
        $goods = GoodsModel::get($id);
        if(empty($goods)){
            $this->error('商品不存在');
        }

        $images=Db::name('GoodsImages')->where('goods_id',$goods['id'])->select();

        return $this->response([
            'goods'=>$goods,
            'images'=>$images
        ]);
    }

}