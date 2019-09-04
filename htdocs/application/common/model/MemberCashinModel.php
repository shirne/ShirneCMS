<?php


namespace app\common\model;


use app\common\core\BaseModel;
use think\Db;
use think\facade\Log;

class MemberCashinModel extends BaseModel
{
    protected $autoWriteTimestamp = true;
    
    public static function init()
    {
        self::event('after_insert', function ($order) {
            Db::name('member')->where('id',$order['member_id'])->setInc('froze_reward',$order['amount']);
            static::sendCashMessage($order['id'],'cash_apply');
        });
    }
    
    protected function triggerStatus($item,$status, $newData=[])
    {
        if($status<0){
            Db::name('member')->where('id',$item['member_id'])->setDec('froze_reward',$item['amount']);
            static::sendCashMessage($item['id'],'cash_fail');
        }elseif($status == 1){
            Db::name('member')->where('id',$item['member_id'])->setInc('total_cashin',$item['amount']);
            if($item['cashtype']=='wechat'){
            
                
            }elseif($item['cashtype']=='alipay'){
            
            }else{
                Db::name('member')->where('id',$item['member_id'])->setDec('froze_reward',$item['amount']);
                money_log($item['member_id'],$item['amount'],'提现成功','cash',0,'reward');
                
            }
            static::sendCashMessage($item['id'],'cash_audit');
        }
        return true;
    }
    
    public static function sendCashMessage($order, $type)
    {
        if(is_string($order) || is_numeric($order)){
            $order = Db::name('memberCashin')->where('id',$order)->find();
        }
        if(empty($order)){
            return false;
        }
        $fans = MemberOauthModel::where('member_id',$order['member_id'])->select();
        $msgdata=[];
        foreach ($fans as $fan){
            $wechat = WechatModel::where('id',$fan['type_id'])->find();
            if(empty($wechat['appid']) || empty($wechat['appsecret']))continue;
            if(!empty($order['appid']) && $order['appid']!=$wechat['appid'])continue;
            $tplset = WechatTemplateMessageModel::getTpls($fan['type_id'],$type);
            if(empty($tplset) || empty($tplset['template_id']))continue;
            
            $tplset['keywords']=static::transkey($tplset['keywords']);
            
            if(empty($msgdata)){
                $msgdata['amount']=number_format($order['amount']*.01,2);
                $msgdata['cash_fee']=number_format($order['cash_fee']*.01,2);
                $msgdata['real_amount']=number_format($order['real_amount']*.01,2);
                
                $msgdata['create_date'] = date('Y-m-d H:i:s',$order['create_time']);
                $msgdata['audit_date'] = date('Y-m-d H:i:s',$order['audit_time']);
                $msgdata['payment_date'] = date('Y-m-d H:i:s',$order['payment_time']);
                $msgdata['fail_date'] = date('Y-m-d H:i:s',$order['fail_time']);
                
                if($order['status']>0){
                    $msgdata['audit_result']='审核通过';
                }else{
                    $msgdata['audit_result']='审核未通过';
                }
                $msgdata['remark']=$order['reason'];
                
                if($order['cashtype']=='wechat') {
                    $msgdata['cashtype'] = '微信零钱';
                    $msgdata['cash_to'] = $fan['nickname'];
                }elseif($order['cashtype']=='alipay'){
                    $msgdata['cashtype'] = '支付宝余额';
                    $msgdata['cash_to'] = $fan['cardno'];
                }else{
                    $msgdata['payment_date']='预计24小时内到账';
                    $msgdata['cashtype']='银行卡';
                    $msgdata['cash_to']=$order['bank'].'/'.maskphone($order['cardno']);
                }
                
                $msgdata['page']='/pages/team/cash-logs';
                
            }
            
            //小程序下如果未获得form_id，需要从支付信息中获取 prepay_id
            if($wechat['account_type'] == 'miniprogram' || $wechat['account_type'] == 'minigame'){
                $msgdata['form_id']=$order['form_id'];
            }
            
            $return = WechatTemplateMessageModel::sendTplMessage($wechat,$tplset, $msgdata, $fan['openid']);
            if($return){
                return $return;
            }
            
        }
        return false;
    }
    
    protected static function transkey($keywords){
        $maps=[
            'amount'=>['提现金额','订单金额'],
            'create_date'=>['申请时间','提现申请时间','提现时间'],
            'audit_date'=>['审核时间'],
            'audit_result'=>['审核结果'],
            'cashtype'=>['提现方式'],
            'cash_fee'=>['提现费率','手续费'],
            'real_amount'=>['到账金额','实际到账'],
            'payment_date'=>['到账时间','预计到账时间'],
            'cash_to'=>['提现至'],
            'remark'=>['失败原因','注意事项','备注']
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
}