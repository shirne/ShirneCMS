<?php

namespace addon\credit_shop;

use addon\base\BaseAddon;
use app\common\model\CreditOrderModel;
use think\facade\Db;
use think\facade\Log;

class CreditShopAddon extends BaseAddon{
    
    public function task(){
        Log::record('credit_shop自动执行任务','task');
        $time = time();

        $creditOrders=Db::name('creditOrder')->where('status',0)
                ->where('create_time','<',time()-7*60)->limit(20)->select();
        foreach ($creditOrders as $order){
            CreditOrderModel::getInstance()->updateStatus(['status'=>-1,'cancel_time'=>time(),'reason'=>'订单长时间未支付自动取消'],['order_id'=>$order['order_id']]);
            if(time()-$time>50){
                exit;
            }
        }
    }
}