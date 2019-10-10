<?php

namespace app\task\controller;

use app\common\model\OrderModel;
use app\common\model\PayOrderModel;
use app\common\model\PostageModel;
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
        $dbs=[];
    
        foreach ($dbs as $sql){
            Db::execute($sql);
        }
        exit;
    }
    
    public function model(){
        $parents=getMemberParents(request()->param('id'),0,false);
        var_dump($parents);
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