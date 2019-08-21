<?php

namespace app\api\controller\member;


use app\api\Controller\AuthedController;
use app\common\model\OrderModel;
use think\Db;

class OrderController extends AuthedController
{
    public function index($status=''){
        $model=Db::name('Order')->where('member_id',$this->user['id'])
            ->where('delete_time',0);
        if($status>0){
            $model->where('status',$status-1);
        }
        $orders =$model->order('status ASC,create_time DESC')->paginate();
        if(!empty($orders) && !$orders->isEmpty()) {
            $order_ids = array_column($orders->items(), 'order_id');
            $products = Db::view('OrderProduct', '*')
                ->view('Product', ['id' => 'orig_product_id', 'update_time' => 'orig_product_update'], 'OrderProduct.product_id=Product.id', 'LEFT')
                ->view('ProductSku', ['sku_id' => 'orig_sku_id', 'price' => 'orig_product_price'], 'ProductSku.sku_id=OrderProduct.sku_id', 'LEFT')
                ->whereIn('OrderProduct.order_id', $order_ids)
                ->select();
            $products=array_index($products,'order_id',true);
            $orders->each(function($item) use ($products){
                $item['products']=isset($products[$item['order_id']])?$products[$item['order_id']]:[];
                return $item;
            });
        }

        $countlist=Db::name('Order')->where('member_id',$this->user['id'])
            ->group('status')->field('status,count(order_id) as order_count')->paginate(10);
        $counts=[0,0,0,0,0,0,0];
        foreach ($countlist as $row){
            $counts[$row['status']]=$row['order_count'];
        }
        return $this->response([
            'lists'=>$orders->items(),
            'page'=>$orders->currentPage(),
            'count'=>$orders->total(),
            'total_page'=>$orders->lastPage(),
            'counts'=>$counts
        ]);
    }

    public function view($id){
        $order=Db::name('Order')->where('order_id',intval($id))->find();
        if(empty($order) || $order['delete_time']>0){
            $this->error('订单不存在或已删除',0);
        }
        $order['products']=Db::view('OrderProduct', '*')
            ->view('Product', ['id' => 'orig_product_id', 'update_time' => 'orig_product_update'], 'OrderProduct.product_id=Product.id', 'LEFT')
            ->view('ProductSku', ['sku_id' => 'orig_sku_id', 'price' => 'orig_product_price'], 'ProductSku.sku_id=OrderProduct.sku_id', 'LEFT')
            ->where('OrderProduct.order_id', $order['order_id'])
            ->select();
        return $this->response($order);
    }
    
    public function cancel($id, $reason=''){
        $order=OrderModel::get(intval($id));
        if(empty($order) || $order['delete_time']>0){
            $this->error('订单不存在或已删除',0);
        }
        if($order['status'] != 0){
            $this->error('订单状态错误',0);
        }
        $success = $order->save(['status'=>-2,'reason'=>$reason]);
        if($success){
            $this->success('订单已取消');
        }else{
            $this->error('取消失败');
        }
    }
    
    public function refund($id, $reason=''){
        $order=OrderModel::get(intval($id));
        if(empty($order) || $order['delete_time']>0){
            $this->error('订单不存在或已删除',0);
        }
        if($order['status'] < 1){
            $this->error('订单状态错误',0);
        }
        
        //退款
        $success = $order->save(['status'=>-3,'reason'=>$reason]);
        if($success){
            
            $this->success('订单已申请退款');
        }else{
            $this->error('取消失败');
        }
    }
    
    public function express($id){
        $order=OrderModel::get(intval($id));
        if(empty($order) || $order['delete_time']>0){
            $this->error('订单不存在或已删除',0);
        }
        $express=[];
        if($order['status']>1 && !empty($order['express_no'])){
            $express = $order->fetchExpress();
        }
        return $this->response($express);
    }
    
    public function confirm($id){
        $order=OrderModel::get(intval($id));
        if(empty($order) || $order['delete_time']>0){
            $this->error('订单不存在或已删除',0);
        }
        if($order['status'] < 1){
            $this->error('订单状态错误',0);
        }
        $success = $order->save(['status'=>4]);
        if($success){
            $this->success('确认成功');
        }else{
            $this->error('确认失败');
        }
        
    }
}