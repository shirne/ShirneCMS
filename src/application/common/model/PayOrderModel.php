<?php

namespace app\common\model;

use EasyWeChat\Factory;
use app\common\core\BaseModel;
use think\Db;
use think\facade\Log;

/**
 * Class PayOrderModel
 * @package app\common\model
 */
class PayOrderModel extends BaseModel
{
    const PAY_TYPE_WECHAT = 'wechat';
    const PAY_TYPE_ALIPAY = 'alipay';
    protected $type = ['pay_data' => 'array'];

    public static $orderTypes = [
        'order' => [
            'title' => '商城订单',
            'prefix' => '',
            'class' => 'app\common\model\OrderModel'
        ],
        'recharge' => [
            'title' => '充值订单',
            'prefix' => 'CZ_',
            'class' => 'app\common\model\MemberRechargeModel'
        ],
        'upgrade' => [
            'title' => '升级订单',
            'prefix' => 'UL_',
            'class' => 'app\common\model\MemberLevelLogModel'
        ],
    ];
    public static $payTypes = [
        'wechat' => '微信支付',
        'alipay' => '支付宝'
    ];

    public static function register($type, $title, $prefix, $orderClass)
    {
        static::$orderTypes[$type] = [
            'title' => $title,
            'prefix' => $prefix,
            'class' => $orderClass
        ];
    }

    private static function create_no()
    {
        $maxid = Db::name('payOrder')->max('id');
        if (empty($maxid)) $maxid = 0;
        return date('YmdHis') . self::pad_orderid($maxid + 1, 4);
    }
    private static function pad_orderid($id, $len = 4)
    {
        $strlen = strlen($id);
        return $strlen < $len ? str_pad($id, $len, '0', STR_PAD_LEFT) : substr($id, $strlen - $len);
    }

    public function createFromOrder($payid, $paytype, $orderno, $trade_type = '')
    {
        $ordertype = '';
        $orderid = 0;
        foreach (static::$orderTypes as $k => $oType) {
            if (!empty($oType['prefix'])) {
                if (strpos($orderno, $oType['prefix']) === 0) {
                    $ordertype = $k;
                    $orderno = intval(substr($orderno, strlen($oType['prefix'])));

                    $order = call_user_func([$oType['class'], 'getOrder'], $orderno);

                    if (empty($order)) {
                        $this->setError('订单已失效或不存在!');
                        return false;
                    }
                    $orderid = $order['order_id'];
                    break;
                }
            }
        }
        if (empty($k)) {
            $ordertype = 'order';
            $order = OrderModel::get($orderno);
            if (!empty($order)) {
                $orderid = $order['order_id'];
                $order['payamount'] = $order['payamount'] - $order['payedamount'];
            }
        }

        if (empty($order) || $order['status'] < 0) {
            $this->setError('订单已失效或不存在!');
            return false;
        }

        if ($order['status'] > 0) {
            $this->setError('订单已支付!', 8);
            return false;
        }

        if ($order['payamount'] <= 0) {
            $this->triggerStatus(['order_type' => $ordertype, 'order_id' => $orderid, 'pay_time' => time()], 1);
            $this->setError('订单支付成功!', 9);

            return false;
        }

        return self::createOrder(
            $paytype,
            $payid,
            $ordertype,
            $orderid,
            $order['payamount'],
            $order['member_id'],
            $trade_type
        );
    }

    /**
     * 创建支付单
     * @param $paytype string 支付类型
     * @param $payid int 支付号
     * @param $type string 订单类型 order/recharge
     * @param $order_id int 订单表的id
     * @param $amount int 金额(分)
     * @param $member_id int 会员id
     * @param $trade_type string 交易类型
     * @param $data
     * @return static
     */
    public static function createOrder($paytype, $payid, $type, $order_id, $amount, $member_id, $trade_type = '', $data = [])
    {

        return static::create([
            'member_id' => $member_id,
            'pay_type' => $paytype,
            'pay_id' => $payid,
            'trade_type' => $trade_type,
            'order_no' => static::create_no(),
            'order_type' => $type,
            'order_id' => $order_id,
            'create_time' => time(),
            'pay_data' => $data,
            'pay_amount' => round($amount * 100)
        ]);
    }

    /**
     * 退款
     */
    public static function refund($orderid, $order_type, $reason)
    {
        $payorder = Db::name('payOrder')->where('order_type', $order_type)
            ->where('status', 1)
            ->where('order_id', $orderid)
            ->find();

        if (!empty($payorder)) {
            if ($payorder['pay_type'] == 'wechat') {
                static $apps = [];
                $appid = $payorder['pay_id'];
                if (!$appid) {
                    Log::error('订单 ' . $order_type . ' ' . $orderid . '退款失败,退款配置错误');
                    return false;
                }
                if (!isset($apps[$appid])) {
                    $apps[$appid] = WechatModel::createApp($appid, true, ['notify' => url('api/wechat/refund', ['hash' => '__HASH__'], true, true), 'use_cert' => true]);
                }
                if ($apps[$appid]) {
                    $refund_id = PayOrderRefundModel::createFromPayOrder($payorder, $reason);
                    if ($refund_id > 0) {
                        $refund = PayOrderRefundModel::get($refund_id);
                        $result = $apps[$appid]->refund->byOutTradeNumber($payorder['order_no'], $refund['refund_no'], $payorder['pay_amount'], $refund['refund_fee'] * 100, [
                            'refund_desc' => $reason,
                        ]);
                        if ($result['return_code'] == 'SUCCESS') {
                            if ($result['result_code'] == 'SUCCESS') {
                                Db::name('payOrder')->where('id', $orderid)->update(['is_refund' => 1, 'refund_fee' => ['INC', $refund['refund_fee']]]);
                                Db::name('payOrderRefund')->where('id', $refund_id)->update(['status' => 1, 'refund_result' => $result['refund_id']]);
                                return true;
                            } else {
                                Db::name('payOrderRefund')->where('id', $refund_id)->update(['status' => -1, 'refund_result' => $result['err_code'] . ':' . $result['err_code_des']]);
                            }
                        } else {
                            Db::name('payOrderRefund')->where('id', $refund_id)->update(['status' => -1, 'refund_result' => $result['return_msg']]);
                        }
                    } else {
                        Log::error('订单 ' . $order_type . ' ' . $orderid . '退款失败,退款单创建失败');
                    }
                } else {
                    Log::error('订单 ' . $order_type . ' ' . $orderid . '退款失败,退款配置错误');
                    return false;
                }
            } else {
                Log::error('订单 ' . $order_type . ' ' . $orderid . '退款失败,暂不支持支付方式 ' . $payorder['pay_type']);
            }
        } else {
            Log::error('订单 ' . $order_type . ' ' . $orderid . '退款失败,未找到支付单');
        }
        return false;
    }

    protected function triggerStatus($item, $status, $newData = [])
    {
        parent::triggerStatus($item, $status, $newData);
        if ($status == 1) {
            $paytime = isset($newData['pay_time']) ? $newData['pay_time'] : 0;
            if (!$paytime) $paytime = time();
            if (isset(static::$orderTypes[$item['order_type']])) {
                $oType = static::$orderTypes[$item['order_type']];
                $order = call_user_func([$oType['class'], 'getOrder'], $item['order_id']);
            }

            if (!empty($order)) {
                $order->onPayResult($item['pay_type'], $paytime, $item['pay_amount'] / 100);
            } else {
                Log::warning('order ' . $item['order_type'] . '/' . $item['order_id'] . ' error');
            }
        }
    }

    public function getSignedData($result, $key)
    {
        $params = [
            'appId' => $result['appid'],
            'timeStamp' => time(),
            'nonceStr' => $result['nonce_str'],
            'package' => 'prepay_id=' . $result['prepay_id'],
            'signType' => 'MD5'
        ];
        ksort($params);
        $string = $this->toUrlParams($params) . "&key=" . $key;
        $params['paySign'] = strtoupper(md5($string));
        return $params;
    }

    protected function toUrlParams($arr)
    {
        $buff = "";
        foreach ($arr as $k => $v) {
            if ($k != "sign" && $v != "" && !is_array($v)) {
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }

    public function checkStatus()
    {
        if (!$this['id'] || $this['status'] != 0) {
            $this->setError('当前状态错误');
            return false;
        }

        if (!$this['pay_id']) {
            $this->setError('支付信息错误');
            return false;
        }

        if ($this['pay_type'] == 'wechat') {
            $wechat = WechatModel::get($this['pay_id']);
            $config = WechatModel::to_pay_config($wechat);
            $app = Factory::payment($config);

            $result = $app->order->queryByOutTradeNumber($this['order_no']);
            if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS') {
                if ($result['trade_state'] == 'SUCCESS') {
                    $this->updateStatus([
                        'status' => 1,
                        'pay_time' => time(),
                        'pay_bill' => $result['transaction_id'],
                        'time_end' => $result['time_end']
                    ]);
                } else {
                    $this->updateStatus(['status' => -1]);
                    $this->setError($result['trade_state_desc']);
                }
            } else {
                $this->setError($result['err_code_des'] ?: $result['return_msg']);
                return false;
            }
        } elseif ($this['pay_type'] == 'alipay') {
        }

        return true;
    }

    public static function filterTypeAndId($type, $id)
    {
        return static::where('order_type', $type)
            ->where('order_id', $id);
    }
}
