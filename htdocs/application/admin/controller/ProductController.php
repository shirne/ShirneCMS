<?php
/**
 * 商品管理
 * User: shirne
 * Date: 2018/5/11
 * Time: 17:47
 */

namespace app\admin\controller;


use think\Db;

class ProductController extends BaseController
{
    public function index($key='',$cate_id=0){
        $model = Db::view('product','*')
            ->view('productCategory',['name'=>'category_name','title'=>'category_title'],'product.cate_id=productCategory.id','LEFT');
        $where=array();
        if(!empty($key)){
            $where[]=['product.title|productCategory.title','like',"%$key%"];
        }
        if($cate_id>0){
            $where[]=['product.cate_id','in',getSubCateids($cate_id)];
        }

        $lists=$model->where($where)->paginate(10);

        $this->assign('lists',$lists);
        $this->assign('page',$lists->render());
        $this->assign('keyword',$key);
        $this->assign('cate_id',$cate_id);
        $this->assign("category",getProductCategories());

        return $this->fetch();
    }
}