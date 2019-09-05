<?php


namespace app\common\model;


use app\common\core\BaseModel;
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
    
    /**
     * 小程序预设模板消息
     * @return array
     */
    public static function miniprogramTpls(){
        return [
            'order_need_pay'=>['title'=>'待付款提醒','title_id'=>'AT0008','keywords'=>'订单号、待付金额、商品详情、支付提醒'],
            'order_payed'=>['title'=>'订单支付成功通知','title_id'=>'AT0009','keywords'=>'订单号码、订单金额、下单时间、物品名称'],
            'order_deliver'=>['title'=>'订单发货提醒','title_id'=>'AT0007','keywords'=>'快递公司、发货时间、购买时间、物品名称'],
            'order_complete'=>['title'=>'订单完成通知','title_id'=>'AT0257','keywords'=>'订单号码、订单金额、商品名称、确认时间'],
            'order_cancel'=>['title'=>'订单取消通知','title_id'=>'AT0024','keywords'=>'订单编号、订单金额、物品详情、取消原因'],
    
            'cash_apply'=>['title'=>'提现申请通知','title_id'=>'AT0324','keywords'=>'提现时间、提现金额、提现方式、提现费率、实际到账、预计到账时间'],
            'cash_audit'=>['title'=>'提现审核通知','title_id'=>'AT1652','keywords'=>'申请时间、提现金额、审核时间、审核结果、注意事项'],
            'cash_fail'=>['title'=>'提现失败通知','title_id'=>'AT1242','keywords'=>'提现时间、提现金额、提现方式、失败原因'],
            'cash_success'=>['title'=>'提现到账通知','title_id'=>'AT0830','keywords'=>'提现申请时间、提现金额、手续费、到账金额、到账时间、提现至、备注'],
        ];
    }
    
    public static function serviceTpls(){
        return [
            'order_need_pay'=>['title'=>'待付款提醒','title_id'=>'AT0008','keywords'=>'订单号、待付金额、商品详情、支付提醒'],
            'order_payed'=>['title'=>'订单支付成功通知','title_id'=>'AT0009','keywords'=>'订单号码、订单金额、下单时间、物品名称'],
            'order_deliver'=>['title'=>'订单发货提醒','title_id'=>'AT0007','keywords'=>'快递公司、发货时间、购买时间、物品名称'],
            'order_complete'=>['title'=>'订单完成通知','title_id'=>'AT0257','keywords'=>'订单号码、订单金额、商品名称、确认时间'],
            'order_cancel'=>['title'=>'订单取消通知','title_id'=>'AT0024','keywords'=>'订单编号、订单金额、物品详情、取消原因'],
            
            'cash_apply'=>['title'=>'提现申请通知','title_id'=>'AT0324','keywords'=>'提现时间、提现金额、提现方式、提现费率、实际到账、预计到账时间'],
            'cash_audit'=>['title'=>'提现审核通知','title_id'=>'AT1652','keywords'=>'申请时间、提现金额、审核时间、审核结果、注意事项'],
            'cash_fail'=>['title'=>'提现失败通知','title_id'=>'AT1242','keywords'=>'提现时间、提现金额、提现方式、失败原因'],
            'cash_success'=>['title'=>'提现到账通知','title_id'=>'AT0830','keywords'=>'提现申请时间、提现金额、手续费、到账金额、到账时间、提现至、备注'],
        ];
    }
    
    public static function sendTplMessage($wechat,$tplset, $msgdata, $openid){
        if(empty($openid))return false;
        if(empty($tplset) || empty($tplset['template_id']))return false;
        //Log::record(var_export(func_get_args(),TRUE));
        
        $app = WechatModel::createApp($wechat);
        if(empty($app))return false;
        
        $data = [];
        
        foreach ($tplset['keywords'] as $index=>$keyword){
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