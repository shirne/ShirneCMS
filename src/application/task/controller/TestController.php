<?php

namespace app\task\controller;

use app\common\model\MemberModel;
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
        MemberModel::where('referer',1)->update(['froze_credit'=>10]);
        MemberModel::create([
            'username'=>'test01',
            'password'=>'123456',
            'referer'=>1,
            'level_id'=>1,
        ]);
        exit;
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
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
        }
        
        OrderModel::sendOrderMessage(5,'order_deliver');
        exit;
    }

    public function poster($id, $account_id){
        $member = MemberModel::where('id',$id)->find();
        if(!$member || $member['is_agent']<1){
            echo '该用户不是代理，请升级后再分享';
            exit;
        }
        
        if(empty($member['agentcode'])){
            echo '代理信息异常';
            exit;
        }
        $cashkey = 'poster-'.random_str(3).'-'.time();
        cache($cashkey,$member['id'].'-'.$account_id);
        $url = url('task/util/poster',['key'=>$cashkey],true,true);

        $ch = curl_init();
        $headers = array("Content-type: application/json;charset='utf-8'",
            "Accept: application/json",
            "Cache-Control: no-cache","Pragma: no-cache");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
        //curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER , 1 );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch,CURLOPT_TIMEOUT,1);
        //curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
        //curl_setopt($ch, CURLOPT_TIMEOUT_MS, 200);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_NOBODY, 0);
        $result = curl_exec($ch);
        curl_close($ch);

        echo 'OK';
        exit;
    }
}