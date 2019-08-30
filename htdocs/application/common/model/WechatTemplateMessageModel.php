<?php


namespace app\common\model;


use think\Exception;
use think\facade\Log;

class WechatTemplateMessageModel extends BaseModel
{
    protected $autoWriteTimestamp = true;
    
    protected static $tpls=[];
    public static function getTpls($wxid,$type=false){
        if(!isset(self::$tpls[$wxid])){
            $tpls=static::where('wechat_id',$wxid)->select();
            self::$tpls[$wxid] = array_column($tpls->toArray(),null,'type');
        }
        if($type){
            return self::$tpls[$wxid][$type]?:null;
        }
        return self::$tpls[$wxid];
    }
    
    protected static function transkey($keywords){
        $maps=[
            'order_no'=>['单号','订单号','订单编号','订单号码'],
            'amount'=>['待付金额','订单金额'],
            'goods'=>['商品详情','物品名称','商品名称','物品详情'],
            'pay_notice'=>['支付提醒'],
            'create_date'=>['下单时间','购买时间'],
            'express'=>['快递公司'],
            'deliver_date'=>['发货时间'],
            'confirm_date'=>['确认时间'],
            'reason'=>['取消原因']
        ];
        if(!is_array($keywords)){
            $keywords = explode('、',$keywords);
        }
        foreach ($keywords as $idx=>$keyword){
            foreach ($maps as $key=>$words){
                if(in_array($keyword,$words)){
                    $keywords[$idx]=$key;
                    break;
                }
            }
        }
        return $keywords;
    }
    
    public static function sendTplMessage($wechat,$tplset, $msgdata, $openid){
        if(empty($openid))return false;
        if(empty($tplset) || empty($tplset['template_id']))return false;
        //Log::record(var_export(func_get_args(),TRUE));
        
        $app = WechatModel::createApp($wechat);
        if(empty($app))return false;
        
        $data = [];
        $keywords = self::transkey($tplset['keywords']);
        
        foreach ($keywords as $index=>$keyword){
            $data['keyword'.($index+1)]=[
                'value'=>$msgdata[$keyword]?:'-'
            ];
        }
        $tplargs=[
            'touser'=>$openid,
            'template_id'=>$tplset['template_id'],
            'data'=>$data
        ];
        if(isset($msgdata['page'])){
            $tplargs['page']=$msgdata['page'];
        }
        if(isset($msgdata['form_id'])){
            $tplargs['form_id']=$msgdata['form_id'];
        }
        try {
            $result = $app->template_message->send($tplargs);
            if($result['errcode']==0) {
                return true;
            }
            Log::record(var_export($result,true));
        }catch(\Exception $e){
            Log::record($e->getMessage());
        }
        return false;
    }
}