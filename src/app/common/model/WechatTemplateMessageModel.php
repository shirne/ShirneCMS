<?php


namespace app\common\model;


use app\common\core\BaseModel;
use think\Exception;
use think\facade\Log;

class WechatTemplateMessageModel extends BaseModel
{
    protected $name = 'wechat_template_message';
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
            'order_need_pay'=>['title'=>'订单待付款提醒','title_id'=>'OPENTM412548551','keywords'=>'商户名称、订单金额、订单编号、订单日期'],
            'order_payed'=>['title'=>'订单支付成功提醒','title_id'=>'OPENTM416836000','keywords'=>'订单编号、商品名称、订单总价、订单状态、下单时间'],
            'order_deliver'=>['title'=>'发货提醒','title_id'=>'OPENTM414274800','keywords'=>'商品名、状态、物流公司、快递单号'],
            'order_complete'=>['title'=>'订单完成通知','title_id'=>'OPENTM410586294','keywords'=>'订单编号、订单详情、订单金额、完成时间'],
            'order_cancel'=>['title'=>'订单取消通知','title_id'=>'TM00850','keywords'=>'订单金额、商品详情、收货信息、订单编号'],
            
            'order_commission'=>['title'=>'分销成功提醒','title_id'=>'OPENTM402027183','keywords'=>'商品信息、商品单价、商品佣金、分销时间'],
            
            'cash_apply'=>['title'=>'提现申请通知','title_id'=>'OPENTM412896310','keywords'=>'提现金额、提现时间、提现手续费、预计到账金额、预计到账时间'],
            'cash_audit'=>['title'=>'提现审核通知','title_id'=>'OPENTM411835838','keywords'=>'提现金额、申请时间、审核状态、原因说明'],
            'cash_fail'=>['title'=>'提现失败通知','title_id'=>'OPENTM416674061','keywords'=>'提现金额、提现时间、提现状态、失败原因'],
            'cash_success'=>['title'=>'提现到账通知','title_id'=>'OPENTM417935160','keywords'=>'提现时间、提现方式、提现金额、提现手续费、实际到账金额'],
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
        if($wechat['account_type'] == 'miniprogram' || $wechat['account_type'] == 'minigame'){
            if( isset($msgdata['page']) ){
                $tplargs['page']=$msgdata['page'];
            }
            if(isset($msgdata['form_id'])){
                $tplargs['form_id']=$msgdata['form_id'];
            }
        }else{
            if(!empty($msgdata['appid']) && isset($msgdata['page'])){
                $tplargs['miniprogram']=[
                    'pagepath'=>$msgdata['page'],
                    'appid'=>$msgdata['appid']
                ];
            }
            if(isset($msgdata['url'])){
                $tplargs['url']=$msgdata['url'];
            }
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