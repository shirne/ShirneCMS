<?php

namespace app\api\controller\member;


use app\api\controller\AuthedController;
use app\common\model\OrderModel;
use app\common\model\OrderRefundModel;
use think\facade\Db;

class OrderController extends AuthedController
{
    public function index($status='',$pagesize=10){
        $model=Db::name('Order')->where('member_id',$this->user['id'])
            ->where('delete_time',0);
        if($status !== ''){
            $model->where('status',intval($status));
        }
        $orders =$model->order('status ASC,create_time DESC')->paginate($pagesize);
        if(!empty($orders) && !$orders->isEmpty()) {
            $order_ids = array_column($orders->all(), 'order_id');
            $products = Db::view('OrderProduct', '*')
                ->view('Product', ['id' => 'orig_product_id', 'update_time' => 'orig_product_update'], 'OrderProduct.product_id=Product.id', 'LEFT')
                ->view('ProductSku', ['sku_id' => 'orig_sku_id', 'price' => 'orig_product_price'], 'ProductSku.sku_id=OrderProduct.sku_id', 'LEFT')
                ->whereIn('OrderProduct.order_id', $order_ids)
                ->select();
            $products=array_index($products,'order_id',true);
            $orders->each(function($item) use ($products){
                $item['product_count']=isset($products[$item['order_id']])?array_sum(array_column($products[$item['order_id']],'count')):0;
                $item['products']=isset($products[$item['order_id']])?$products[$item['order_id']]:[];
                return $item;
            });
        }
        
        $counts = OrderModel::getCounts($this->user['id']);
        return $this->response([
            'lists'=>$orders->all(),
            'page'=>$orders->currentPage(),
            'count'=>$orders->total(),
            'total_page'=>$orders->lastPage(),
            'counts'=>$counts
        ]);
    }
    
    public function counts(){
        $counts = OrderModel::getCounts($this->user['id']);
        return $this->response($counts);
    }

    public function view($id){
        $order=Db::name('Order')->where('order_id',intval($id))->find();
        if(empty($order) || $order['member_id']!=$this->user['id'] || $order['delete_time']>0){
            $this->error('订单不存在或已删除',0);
        }
        $order['products']=Db::view('OrderProduct', '*')
            ->view('Product', ['id' => 'orig_product_id', 'update_time' => 'orig_product_update'], 'OrderProduct.product_id=Product.id', 'LEFT')
            ->view('ProductSku', ['sku_id' => 'orig_sku_id', 'price' => 'orig_product_price'], 'ProductSku.sku_id=OrderProduct.sku_id', 'LEFT')
            ->where('OrderProduct.order_id', $order['order_id'])
            ->select()->all();
        $order['product_count']=empty($order['products'])?0:array_sum(array_column($order['products'],'count'));
        return $this->response($order);
    }
    
    public function cancel($id, $reason=''){
        $order=OrderModel::where('order_id',intval($id))->find();
        if(empty($order) || $order['member_id']!=$this->user['id'] || $order['delete_time']>0){
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
    
    public function refund($id){
        $order=OrderModel::where('order_id',intval($id))->find();
        if(empty($order) || $order['delete_time']>0){
            $this->error('订单不存在或已删除',0);
        }
        if($order['status'] < 1){
            $this->error('订单状态错误',0);
        }

        $params = $this->request->param();
        $params['member_id']=$this->user['id'];
        
        //退款
        if($this->request->isPost()){
            try{
                $success = OrderRefundModel::createRefund($order, $params);
            }catch(\Exception $e){
                $this->error($e->getMessage());
            }
            if($success){
                $this->success('订单已申请退款');
            }else{
                $this->error('申请失败');
            }
        }
        
        $refund = OrderRefundModel::where('order_id',$id)->find();
        return $this->response(['refund'=>$refund]);
    }
    
    public function express($id){
        $order=OrderModel::where('order_id',intval($id))->find();
        if(empty($order) || $order['member_id']!=$this->user['id'] || $order['delete_time']>0){
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
            $companies=config('express');
            $returnData['express']=$companies[$returnData['express_code']]?:'其它';
        }
        
        $products=Db::name('OrderProduct')->where('order_id', $order['order_id'])->select()->all();
        
        if(!empty($products)) {
            $product = current($products);
            if (count($products) > 1) {
                $totalcount = array_sum(array_column($products, 'count'));
                $product = current($products);
                $title = $product['product_title'] . ' 等 ' . $totalcount . ' 件商品';
            } else {
                $title = $product['product_title'] . ' ' . $product['count'] . ' 件';
            }
            $image = $product['product_image'];
            
            //$express['order']=$order;
            $returnData['product']=[
                'title'=>$title,
                'image'=>$image
            ];
        }
        
        return $this->response($returnData);
    }
    
    public function confirm($id){
        $order=OrderModel::where('order_id',intval($id))->find();
        if(empty($order) || $order['member_id']!=$this->user['id'] || $order['delete_time']>0){
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
    
    public function delete($id){
        $order=OrderModel::where('order_id',intval($id))->find();
        if(empty($order) || $order['member_id']!=$this->user['id'] || $order['delete_time']>0){
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
    
    //todo 订单评论
    public function comment(){
    
    }
}