<?php


namespace app\common\model;


use app\common\core\BaseModel;
use think\facade\Log;

class WechatTemplateMessageModel extends BaseModel
{
    protected $autoWriteTimestamp = true;

    protected static $tpls = [];
    public static function getTpls($wxid, $type = false)
    {
        if (!isset(self::$tpls[$wxid])) {
            $tpls = static::where('wechat_id', $wxid)->select();
            self::$tpls[$wxid] = array_column($tpls->toArray(), null, 'type');
        }
        if ($type && isset(self::$tpls[$wxid])) {
            return self::$tpls[$wxid][$type] ?: null;
        }
        return self::$tpls[$wxid];
    }

    /**
     * 小程序预设模板消息
     * @return array
     */
    public static function miniprogramTpls()
    {
        return [
            'order-state' => ['title' => '订单状态变更通知', 'tid' => '37634', 'keywords' => '订单号、订单状态、变更时间'],
            'order-place' => ['title' => '下单成功通知', 'tid' => '38147', 'keywords' => '订单编号、产品名称、数量、姓名、地址'],
            'order-payed' => ['title' => '支付成功通知', 'tid' => '39828', 'keywords' => '商品名称、商品数量、支付金额、支付时间、订单号'],
            'order-deliver' => ['title' => '订单发货提醒', 'tid' => '37171', 'keywords' => '订单号、发货时间、快递单号、快递公司、商品'],
            'order-complete' => ['title' => '订单完成通知', 'tid' => '38077', 'keywords' => '订单编号、产品名称、数量、状态、完成时间'],
            // 'order-cancel'=>['title'=>'订单取消通知','tid'=>'AT0024','keywords'=>'订单编号、订单金额、物品详情、取消原因'],

            // 'cash-apply'=>['title'=>'提现申请通知','tid'=>'AT0324','keywords'=>'提现时间、提现金额、提现方式、提现费率、实际到账、预计到账时间'],
            // 'cash-audit'=>['title'=>'提现审核通知','tid'=>'AT1652','keywords'=>'申请时间、提现金额、审核时间、审核结果、注意事项'],
            // 'cash-fail'=>['title'=>'提现失败通知','tid'=>'AT1242','keywords'=>'提现时间、提现金额、提现方式、失败原因'],
            // 'cash-success'=>['title'=>'提现到账通知','tid'=>'AT0830','keywords'=>'提现申请时间、提现金额、手续费、到账金额、到账时间、提现至、备注'],

            'invite' => ['title' => '好友邀请结果通知', 'tid' => '38632', 'keywords' => '邀请结果、温馨提醒'],
        ];
    }

    public static function serviceTpls()
    {
        return [
            'order_need_pay' => ['title' => '订单待付款提醒', 'tid' => 'OPENTM412548551', 'keywords' => '商户名称、订单金额、订单编号、订单日期'],
            'order_payed' => ['title' => '订单支付成功提醒', 'tid' => 'OPENTM416836000', 'keywords' => '订单编号、商品名称、订单总价、订单状态、下单时间'],
            'order_deliver' => ['title' => '发货提醒', 'tid' => 'OPENTM414274800', 'keywords' => '商品名、状态、物流公司、快递单号'],
            'order_complete' => ['title' => '订单完成通知', 'tid' => 'OPENTM410586294', 'keywords' => '订单编号、订单详情、订单金额、完成时间'],
            'order_cancel' => ['title' => '订单取消通知', 'tid' => 'TM00850', 'keywords' => '订单金额、商品详情、收货信息、订单编号'],

            'order_commission' => ['title' => '分销成功提醒', 'tid' => 'OPENTM402027183', 'keywords' => '商品信息、商品单价、商品佣金、分销时间'],

            'cash_apply' => ['title' => '提现申请通知', 'tid' => 'OPENTM412896310', 'keywords' => '提现金额、提现时间、提现手续费、预计到账金额、预计到账时间'],
            'cash_audit' => ['title' => '提现审核通知', 'tid' => 'OPENTM411835838', 'keywords' => '提现金额、申请时间、审核状态、原因说明'],
            'cash_fail' => ['title' => '提现失败通知', 'tid' => 'OPENTM416674061', 'keywords' => '提现金额、提现时间、提现状态、失败原因'],
            'cash_success' => ['title' => '提现到账通知', 'tid' => 'OPENTM417935160', 'keywords' => '提现时间、提现方式、提现金额、提现手续费、实际到账金额'],
        ];
    }

    public static function sendTplMessage($wechat, $tplset, $msgdata, $openid)
    {
        if (empty($openid)) return false;
        if (empty($tplset) || empty($tplset['template_id'])) return false;
        //Log::info(var_export(func_get_args(),TRUE));

        $app = WechatModel::createApp($wechat);
        if (empty($app)) return false;

        $data = [];

        foreach ($tplset['keywords'] as $index => $keyword) {
            $data['keyword' . ($index + 1)] = [
                'value' => $msgdata[$keyword] ?: '-'
            ];
        }
        $tplargs = [
            'touser' => $openid,
            'template_id' => $tplset['template_id'],
            'data' => $data
        ];
        $client = null;
        if ($wechat['account_type'] == 'miniprogram' || $wechat['account_type'] == 'minigame') {
            if (isset($msgdata['page'])) {
                $tplargs['page'] = $msgdata['page'];
            }
            $client = $app->subscribe_message;
        } else {
            if (!empty($msgdata['appid']) && isset($msgdata['page'])) {
                $tplargs['miniprogram'] = [
                    'pagepath' => $msgdata['page'],
                    'appid' => $msgdata['appid']
                ];
            }
            if (isset($msgdata['url'])) {
                $tplargs['url'] = $msgdata['url'];
            }
            $client = $app->template_message;
        }
        try {
            $result = $client->send($tplargs);
            if ($result['errcode'] == 0) {
                return true;
            }
            Log::warning(var_export($result, true));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
        return false;
    }
}
