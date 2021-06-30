<?php

namespace app\api\controller\member;


use app\api\controller\AuthedController;
use app\common\model\CreditOrderModel;
use think\Db;
use think\response\Json;

/**
 * 积分商城订单管理
 * @package app\api\controller\member
 */
class CreditOrderController extends AuthedController
{
    /**
     * 订单列表
     * @param string $status 
     * @param int $pagesize 
     * @return Json 
     */
    public function index($status='',$pagesize=10){
        $model=Db::name('creditOrder')->where('member_id',$this->user['id'])
            ->where('delete_time',0);
        if($status !== ''){
            $model->where('status',intval($status));
        }
        $orders =$model->order('status ASC,create_time DESC')->paginate($pagesize);
        if(!empty($orders) && !$orders->isEmpty()) {
            $order_ids = array_column($orders->items(), 'order_id');
            $goods = Db::view('creditOrderGoods', '*')
                ->view('goods', ['id' => 'orig_goods_id', 'update_time' => 'orig_goods_update'], 'creditOrderGoods.goods_id=goods.id', 'LEFT')
                ->whereIn('creditOrderGoods.order_id', $order_ids)
                ->select();
            $goods=array_index($goods,'order_id',true);
            $orders->each(function($item) use ($goods){
                $item['goods_count']=isset($goods[$item['order_id']])?array_sum(array_column($goods[$item['order_id']],'count')):0;
                $item['goods']=isset($goods[$item['order_id']])?$goods[$item['order_id']]:[];
                return $item;
            });
        }
        
        $counts = CreditOrderModel::getCounts($this->user['id']);
        return $this->response([
            'lists'=>$orders->items(),
            'page'=>$orders->currentPage(),
            'count'=>$orders->total(),
            'total_page'=>$orders->lastPage(),
            'counts'=>$counts
        ]);
    }
    
    /**
     * 获取各状态订单数量
     * @return Json 
     */
    public function counts(){
        $counts = CreditOrderModel::getCounts($this->user['id']);
        return $this->response($counts);
    }

    /**
     * 获取订单详情
     * @param mixed $id 
     * @return Json 
     */
    public function view($id){
        $order=Db::name('creditOrder')->where('order_id',intval($id))->find();
        if(empty($order) || $order['delete_time']>0){
            $this->error('订单不存在或已删除',0);
        }
        $order['goods']=Db::view('creditOrderGoods', '*')
            ->view('goods', ['id' => 'orig_goods_id', 'update_time' => 'orig_goods_update','vice_title','unit'], 'creditOrderGoods.goods_id=goods.id', 'LEFT')
            ->where('creditOrderGoods.order_id', $order['order_id'])
            ->select();
        $order['goods_count']=empty($order['goods'])?0:array_sum(array_column($order['goods'],'count'));
        return $this->response($order);
    }
    
    /**
     * 取消订单
     * @param mixed $id 
     * @param string $reason 
     * @return void 
     */
    public function cancel($id, $reason=''){
        $order=CreditOrderModel::get(intval($id));
        if(empty($order) || $order['delete_time']>0){
            $this->error('订单不存在或已删除',0);
        }
        if($order['status'] != 0){
            $this->error('订单状态错误',0);
        }
        $success = $order->updateStatus(['status'=>-2,'reason'=>$reason]);
        if($success){
            $this->success('订单已取消');
        }else{
            $this->error('取消失败');
        }
    }
    
    /**
     * 退款申请
     * @param mixed $id 
     * @param string $reason 
     * @return void 
     */
    public function refund($id, $reason=''){
        $order=CreditOrderModel::get(intval($id));
        if(empty($order) || $order['delete_time']>0){
            $this->error('订单不存在或已删除',0);
        }
        if($order['status'] < 1){
            $this->error('订单状态错误',0);
        }
        
        //退款
        $success = $order->updateStatus(['status'=>-3,'reason'=>$reason]);
        if($success){
            
            $this->success('订单已申请退款');
        }else{
            $this->error('取消失败');
        }
    }
    
    /**
     * 获取订单快递信息
     * @param mixed $id 
     * @return Json 
     */
    public function express($id){
        $order=CreditOrderModel::get(intval($id));
        if(empty($order) || $order['delete_time']>0){
            $this->error('订单不存在或已删除',0);
        }
        
        if($order['status']>1 && !empty($order['express_no'])){
            $express = $order->fetchExpress();
        }
        if(empty($express)){
            $express=[];
        }
        $returnData=[
            'traces'=>$express['Traces']?:null,
            'express_code'=>$order->express_code,
            'express_no'=>$order->express_no
        ];
        if(!empty($returnData['express_code'])){
            $companies=config('express.');
            $returnData['express']=$companies[$returnData['express_code']]?:'其它';
        }
        
        $products=Db::name('creditOrderGoods')->where('order_id', $order['order_id'])->select();
        
        if(!empty($products)) {
            $product = current($products);
            if (count($products) > 1) {
                $totalcount = array_sum(array_column($products, 'count'));
                $product = current($products);
                $title = $product['goods_title'] . ' 等 ' . $totalcount . ' 件商品';
            } else {
                $title = $product['goods_title'] . ' ' . $product['count'] . ' 件';
            }
            $image = $product['goods_image'];
            
            //$express['order']=$order;
            $returnData['goods']=[
                'title'=>$title,
                'image'=>$image
            ];
        }
        
        return $this->response($returnData);
    }
    
    /**
     * 确认收货
     * @param mixed $id 
     * @return void 
     */
    public function confirm($id){
        $order=CreditOrderModel::get(intval($id));
        if(empty($order) || $order['delete_time']>0){
            $this->error('订单不存在或已删除',0);
        }
        if($order['status'] < 1){
            $this->error('订单状态错误',0);
        }
        $success = $order->updateStatus(['status'=>4,'confirm_time'=>time()]);
        if($success){
            $this->success('确认成功');
        }else{
            $this->error('确认失败');
        }
    }
    
    /**
     * 删除订单
     * @param mixed $id 
     * @return void 
     */
    public function delete($id){
        $order=CreditOrderModel::get(intval($id));
        if(empty($order) || $order['delete_time']>0){
            $this->error('订单不存在或已删除',0);
        }
        if($order['status'] >-1 || $order['status']<-2){
            $this->error('订单当前不可删除',0);
        }
        $success = $order::where('order_id',intval($id))->useSoftDelete('delete_time',time())->delete();
        if($success){
            $this->success('订单已删除');
        }else{
            $this->error('删除失败');
        }
    }
    
    /**
     * 订单评论 todo
     * @return void 
     */
    public function comment(){
    
    }
}