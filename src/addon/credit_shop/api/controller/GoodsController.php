<?php

namespace addon\credit_shop\api\controller;

use addon\base\BaseController;
use addon\credit_shop\core\facade\CategoryFacade;
use addon\credit_shop\core\model\GoodsModel;
use think\Db;

/**
 * 产品操作接口
 * Class ProductController
 * @package app\api\Controller
 */
class GoodsController extends BaseController
{
    /**
     * 获取所有积分商品分类
     * 格式
     *   0 => 顶级类列表
     *   id => 子类列表
     *   ...
     * @return Json 
     */
    public function get_all_cates(){
        return $this->response(CategoryFacade::getTreedCategory());
    }

    /**
     * 获取指定id的子类，可携带指定数量和筛选条件的文章
     * @param int $pid 
     * @param int $goods_count 携带积分商品数量
     * @param array $filters 携带积分商品筛选条件
     * @return Json 
     */
    public function get_cates($pid=0, $goods_count=0, $filters=[]){
        if($pid!=0 && preg_match('/^[a-zA-Z]\w+/',$pid)){
            $current=CategoryFacade::findCategory($pid);
            if(empty($current)){
                return $this->response([]);
            }
            $pid=$current['id'];
        }
        $cates = CategoryFacade::getSubCategory($pid);
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

    /**
     * 获取积分商品列表，可分页
     * @param string $cate 指定所属的分类，默认包含子类
     * @param string $order 指定排序
     * @param string $keyword 指定关键字
     * @param int $page 指定分页
     * @param int $pagesize 指定获取数量，分页时为每页大小
     * @return Json 
     */
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

    /**
     * 获取积分商品详情
     * @param mixed $id 
     * @return Json 
     */
    public function view($id){
        $goods = GoodsModel::find($id);
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