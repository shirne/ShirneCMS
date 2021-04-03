<?php

namespace addon\shop;

use addon\base\BaseAddon;
use app\common\model\OrderModel;
use think\facade\Db;
use think\facade\Log;

class ShopAddon extends BaseAddon{
    
    public function task(){
        Log::record('shop自动执行任务','task');
        $time = time();
        $isbreaked=false;

        $shopset = getSettings(false, 'shop');

        // 未支付提醒
        $orders = Db::name('Order')->where('status',0)
            ->where('noticed',0)
            ->where('create_time','<',time()-10*60)->select();
        $ids = [];
        foreach ($orders as $order){
            $ids[]=$order['order_id'];
            OrderModel::sendOrderMessage($order,'order_need_pay');
            if(time()-$time>25){
                $isbreaked=true;
                break;
            }
        }
        Db::name('Order')->whereIn('order_id',$ids)->update(['noticed'=>1]);
        if($isbreaked)exit;
    
        if($shopset['shop_order_pay_limit'] > 0){
            $orders = OrderModel::where('status',ORDER_STATUS_UNPAIED)
                ->where('create_time','<',time()-$shopset['shop_order_pay_limit']*60)->select();
            foreach ($orders as $order){
                $order->updateStatus(['status'=>-1,'cancel_time'=>time(),'reason'=>'订单 '.$shopset['shop_order_pay_limit'].' 分钟内未支付自动取消']);
                if(time()-$time>25){
                    $isbreaked=true;
                    break;
                }
            }
        }
        if($isbreaked)exit;

        $orders = OrderModel::where('status',ORDER_STATUS_SHIPED)
            ->where('receive_time','<',time())->where('receive_time','<>',0)->select();
        foreach ($orders as $order){
            $order->updateStatus(['status'=>ORDER_STATUS_RECEIVED,'reason'=>'订单自动收货']);
            if(time()-$time>25){
                $isbreaked=true;
                break;
            }
        }
        if($isbreaked)exit;

        $orders = OrderModel::where('status',ORDER_STATUS_RECEIVED)->where('islock',0)
            ->where('confirm_time','<',time()-$shopset['shop_order_refund_limit']*60*60*24)->select();
        foreach ($orders as $order){
            $order->updateStatus(['status'=>ORDER_STATUS_FINISH,'islock'=>1,'reason'=>'订单自动完成']);

            //todo 默认好评
            
            if(time()-$time>25){
                $isbreaked=true;
                break;
            }
        }
        if($isbreaked)exit;

        $orders = OrderModel::where('status',ORDER_STATUS_FINISH)->where('islock',0)
            ->where('confirm_time','<',time()-$shopset['shop_order_refund_limit']*60*60*24)->select();
        foreach ($orders as $order){
            $order->update(['islock'=>1]);
            if(time()-$time>25){
                $isbreaked=true;
                break;
            }
        }
        if($isbreaked)exit;

        
    }
}