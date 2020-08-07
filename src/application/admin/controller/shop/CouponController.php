<?php

namespace app\admin\controller\shop;

use app\admin\controller\BaseController;
use app\admin\validate\ProductCouponValidate;
use app\common\facade\ProductCategoryFacade;
use app\common\model\ProductCouponModel;
use think\Db;

/**
 * 优惠券管理
 * Class CouponController
 * @package app\admin\controller\shop
 */
class CouponController extends BaseController
{
    /**
     * 管理
     * @param $key
     * @return mixed
     */
    public function index($key=''){
        $model = Db::view('productCoupon','*')
            ->view('productCategory',['title'=>'category_title'],'productCategory.id=productCoupon.cate_id','left')
            ->view('productBrand',['title'=>'brand_title'],'productBrand.id=productCoupon.brand_id','left')
            ->view('product',['title'=>'product_title'],'product.id=productCoupon.product_id','left')
            ->view('productSku','goods_no','productSku.sku_id=productCoupon.sku_id','left');
        if(!empty($key)){
            $model->whereLike('productCoupon.title|productCategory.title|productBrand.title|product.title|productSku.goods_no',"%$key%");
        }
        $lists=$model->order('productCoupon.id DESC')->paginate(15);
        $this->assign('lists',$lists);
        $this->assign('page',$lists->render());
        return $this->fetch();
    }

    /**
     * 添加
     * @return mixed
     */
    public function add(){
        if ($this->request->isPost()) {
            $data=$this->request->post();
            $validate=new ProductCouponValidate();

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }else{
                $model=ProductCouponModel::create($data);
                if ($model['id']) {
                    $this->success(lang('Add success!'), url('shop.coupon/index'));
                } else {
                    $this->error(lang('Add failed!'));
                }
            }
        }
        $model=array('status'=>1,'type'=>1,'bind_type'=>0,'expiry_type'=>1);
        $this->assign('model',$model);
        $this->assign('levels',getMemberLevels());
        $this->assign("category",ProductCategoryFacade::getCategories());
        $this->assign('id',0);
        return $this->fetch('update');
    }

    /**
     * 修改
     * @param int $id
     * @return string
     */
    public function update($id)
    {
        $id = intval($id);
        $model = ProductCouponModel::get($id);

        if ($this->request->isPost()) {
            $data=$this->request->post();
            $validate=new ProductCouponValidate();

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }else{

                try{
                    $model->save($data);
                    
                }catch(\Exception $err){
                    $this->error(lang('Update failed: %',[$err->getMessage()]));
                }
                $this->success(lang('Update success!'), url('shop.coupon/index'));
            }
        }

        if(empty($model)){
            $this->error('优惠券不存在');
        }
        $this->assign('model',$model);
        $this->assign("category",ProductCategoryFacade::getCategories());

        $brand=['id'=>0,'title'=>'请选择品牌'];
        if($model['brand_id']>0){
            $brand = Db::name('productBrand')->where('id',$model['brand_id'])->find();
        }
        $this->assign('brand',$brand);
        $product=['id'=>0,'title'=>'请选择商品'];
        if($model['product_id']>0){
            $product = Db::name('product')->where('id',$model['product_id'])->find();
        }
        $this->assign('product',$product);
        $sku=['id'=>0,'title'=>'请选择SKU'];
        if($model['sku_id']>0){
            $sku = Db::name('productSku')->where('id',$model['sku_id'])->find();
        }
        $this->assign('sku',$sku);
        $this->assign('id',$id);
        $this->assign('model',$model);
        $this->assign("category",ProductCategoryFacade::getCategories());
        $this->assign('levels',getMemberLevels());
        return $this->fetch();

    }

    /**
     * 删除优惠券
     * @param $id
     */
    public function delete($id)
    {
        $id = intval($id);
        $force=$this->request->post('force/d',0);
        $model = Db::name('ProductCoupon');
        $count=Db::name('memberCoupon')->where('coupon_id',$id)->count();
        $result=false;
        if($count<1 || $force!=0) {
            $result = $model->delete($id);
        }else{
            $this->error("优惠券已有会员领取，不可直接删除");
        }
        if($result){
            if($count>0){
                Db::name('MemberCoupon')->where('coupon_id',$id)->delete();
            }
            $this->success(lang('Delete success!'), url('shop.coupon/index'));
        }else{
            $this->error(lang('Delete failed!'));
        }
    }

    /**
     * 已领优惠券
     * @param $gid
     * @return mixed
     */
    public function itemlist($gid,$key='',$member_id=0){
        $gid=intval($gid);
        $group=Db::name('ProductCoupon')->find($gid);
        if(empty($group)){
            $this->error('优惠券不存在');
        }
        $model = Db::view('MemberCoupon mc','*')
            ->view('__MEMBER__ m',['username','realname','nickname','avatar','mobile','level_id'],'m.id = mc.member_id','LEFT')
            ->where('coupon_id',$gid);
        if($member_id > 0){
            $model->where('member_id',$member_id);
        }
        if(!empty($key)){
            $model->whereLike('title|url',"%$key%");
        }
        $lists=$model->order('id DESC')->paginate(15);
        $this->assign('lists',$lists);
        $this->assign('page',$lists->render());
        $this->assign('gid',$gid);
        $this->assign('levels',getMemberLevels());
        return $this->fetch();
    }

    /**
     * 删除已领优惠券
     */
    public function itemdelete($gid,$id)
    {
        $id = intval($id);
        $model = Db::name('MemberCoupon');
        $result = $model->delete($id);
        if($result){
            $this->success(lang('Delete success!'), url('shop.coupon/itemlist',array('gid'=>$gid)));
        }else{
            $this->error(lang('Delete failed!'));
        }
    }
}