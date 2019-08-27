<?php

namespace app\task\controller;

use app\common\model\OrderModel;
use app\common\model\PayOrderModel;
use shirne\common\Image;
use think\Db;
use think\facade\Log;


/**
 * 测试功能
 * Class TestController
 * @package app\task\controller
 */
class TestController
{
    public function image()
    {
        $url = './uploads/article/2019/01/7c9028a9010bbc7cb84056b2ebdfd706.png';
        $image = new Image($url);
        $image->crop(0,0,100,100);
        $image->output();
        exit;
    }
    
    public function updatedb(){
        $dbs=[
            //"alter table sa_order add `form_id` VARCHAR(45) NULL after `delete_time`",
            //"alter table sa_pay_order add `prepay_id` VARCHAR(45) NULL after `pay_type`",
            //"alter table sa_order add `platform` VARCHAR(45) NULL after `order_id`",
            //"alter table sa_order add `noticed` TINYINT NULL DEFAULT 0 after `status`",
            //"alter table sa_order add `comment_time` TINYINT NULL DEFAULT 0 after `confirm_time`",
            //"alter table sa_order add `refund_time` TINYINT NULL DEFAULT 0 after `cancel_time`",
            //"alter table `sa_member_token` ADD `platform` VARCHAR(20) NULL AFTER `member_id`"
        ];
        foreach ($dbs as $sql){
            Db::execute($sql);
        }
        exit;
    }
    
    public function model(){
        exit;
        $paymodel = PayOrderModel::get(10);
        $paymodel->save(['status'=>0]);
        $data = [
            'status'=>1,
            'pay_time'=>time(),
            'pay_bill'=>'4200000396201908281858444566',
            'time_end'=>'20190828004001'
        ];
        try {
            $paymodel->updateStatus($data);
        }catch(\Exception $e){
            Log::record($e->getMessage());
            Log::record($e->getTraceAsString());
        }
        
        OrderModel::sendOrderMessage(5,'order_deliver');
        exit;
    }
}