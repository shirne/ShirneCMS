<?php

namespace app\admin\controller;
use app\admin\validate\ProductCouponValidate;
use think\Db;

/**
 * 优惠券管理
 * Class ProductCouponController
 * @package app\admin\controller
 */
class ProductCouponController extends BaseController
{
    /**
     * 管理
     * @param $key
     * @return mixed
     */
    public function index($key=''){
        $model = Db::name('ProductCoupon');
        if(!empty($key)){
            $model->whereLike('title|flag',"%$key%");
        }
        $lists=$model->order('id DESC')->paginate(15);
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
                if (Db::name("ProductCoupon")->insert($data)) {
                    $this->success(lang('Add success!'), url('productCoupon/index'));
                } else {
                    $this->error(lang('Add failed!'));
                }
            }
        }
        $model=array('status'=>1,'type'=>1,'bind_type'=>0,'expiry_type'=>1);
        $this->assign('model',$model);
        $this->assign('id',0);
        return $this->fetch('update');
    }

    /**
     * 修改
     */
    public function update($id)
    {
        $id = intval($id);

        if ($this->request->isPost()) {
            $data=$this->request->post();
            $validate=new ProductCouponValidate();

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }else{
                $model = Db::name("ProductCoupon");

                $data['id']=$id;
                if ($model->update($data)) {
                    $this->success(lang('Update success!'), url('productCoupon/index'));
                } else {
                    $this->error(lang('Update failed!'));
                }
            }
        }

        $model = Db::name('ProductCoupon')->where('id', $id)->find();
        if(empty($model)){
            $this->error('优惠券不存在');
        }
        $this->assign('model',$model);
        $this->assign('id',$id);
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
            $this->success(lang('Delete success!'), url('productCoupon/index'));
        }else{
            $this->error(lang('Delete failed!'));
        }
    }

    /**
     * 已领优惠券
     * @param $gid
     * @return mixed
     */
    public function itemlist($gid){
        $model = Db::name('MemberCoupon');
        $gid=intval($gid);
        $group=Db::name('ProductCoupon')->find($gid);
        if(empty($group)){
            $this->error('优惠券不存在');
        }
        $where=array('coupon_id'=>$gid);
        if(!empty($key)){
            $where[] = array('title|url','like',"%$key%");
        }
        $lists=$model->where($where)->order('sort ASC,id DESC')->paginate(15);
        $this->assign('lists',$lists);
        $this->assign('page',$lists->render());
        $this->assign('gid',$gid);
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
            $this->success(lang('Delete success!'), url('productCoupon/itemlist',array('gid'=>$gid)));
        }else{
            $this->error(lang('Delete failed!'));
        }
    }
}