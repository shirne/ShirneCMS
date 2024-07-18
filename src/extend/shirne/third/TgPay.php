<?php

namespace shirne\third;

use think\facade\Log;

/**
 * RSA最大加密明文大小
 */
define('MAX_ENCRYPT_BLOCK', 117);

/**
 * RSA最大解密密文大小
 */
define('MAX_DECRYPT_BLOCK', 128);

class TgPay extends ThirdBase
{

    protected $account;
    protected $md5Key;


    public function __construct($options)
    {
        parent::__construct($options);

        //测试环境
        //$this->baseURL = 'https://yapi.newict.com.cn/mock/35/';

        //正式环境
        $this->baseURL = 'http://ipay.833006.net/';

        $this->account = isset($options['account']) ? $options['account'] : '';
        $this->md5Key = isset($options['md5key']) ? $options['md5key'] : '';
    }

    /**
     * @param array $data
     * @return array
     */
    protected function signData($data)
    {
        $data['sign'] = $this->sign($data);
        return $data;
    }

    /**
     * @param array $data
     * @return string
     */
    protected function sign($data)
    {
        ksort($data);
        $str = '';
        foreach ($data as $key => $value) {
            if ($value !== null && $value !== '') {
                $str .= "{$key}={$value}&";
            }
        }
        $str .= "key={$this->md5Key}";
        return strtoupper(md5($str));
    }

    /**
     * 一码付下单
     * @param string $orderNo
     * @param string $amount
     * @param string $body
     * @param string $attach
     * @param string $returnUrl
     * @return array
     * status	number	必须 100成功，101失败	
     * message	string	必须 信息描述	
     * codeUrl	string	必须 URL地址供终端生成二维码or直接打开此url	
     * orderId	string	必须 通莞订单号
     */
    public function allQrcodePay($orderNo, $amount, $body, $attach = '', $returnUrl = '')
    {
        return $this->request('tgPosp/services/payApi/allQrcodePay', [
            'account' => $this->account,
            'lowOrderId' => $orderNo,
            'payMoney' => $amount,
            'body' => $body,
            'attach' => $attach,
            'notifyUrl' => url('api/tgpay/notice', '', true, true),
            'returnUrl' =>  $returnUrl,
        ]);
    }

    /**
     * 订单查询(使用下游订单号)
     * @param string $orderNo
     * @return array @see $this->orderQueryOrig
     */
    public function orderQuery($orderNo)
    {
        return $this->orderQueryOrig([
            'lowOrderId' => $orderNo,
        ]);
    }

    /**
     * 订单查询(使用上游订单号)
     * @param string $upOrderId
     * @return array @see $this->orderQueryOrig
     */
    public function orderQueryByTgOrderId($upOrderId)
    {
        return $this->orderQueryOrig([
            'upOrderId' => $upOrderId,
        ]);
    }

    /**
     * 订单查询(原始接口)
     * @param array $data
     * @return array
     * status	number	必须 100:成功，101：失败	
     * message	string	必须 消息描述	
     * channelOrderId	string	必须 上游订单号	
     * upOrderId	string	必须 通莞订单号	
     * payoffType	string	必须 默认传null	
     * payTime	string	必须 支付时间	
     * openId	string	必须 支付方式为微信支付的时候返回消费者在该商户appid下的唯一标识；支付方式为支付宝是返回用户支付宝的账户名	
     * openid	string	必须 支付方式为微信支付的时候返回消费者在该商户appid下的唯一标识；支付方式为支付宝是返回用户支付宝的账户名	
     * sign	string	必须 签名	
     * settlementChannel	string	必须 结算渠道编号	
     * lowOrderId	string	必须 下游订单号	
     * payMoney	string	必须 支付金额，单位元	
     * payType	string	必须 支付方式0：微信，1：支付宝，2：银联扫 码 6：龙支付 8 ：翼支付，H：数字货币	
     * state	string	必须 0：成功，1：失败，2：已撤销 4:待支付 5:已转入退款 6:已转入部分退款	
     * attach	string	非必须 订单备注	
     * account	string	必须 聚合账户	
     * channelId	string	必须 支付方式例：WX、ZFB、YZF、LZF、YLZF	
     * discountInfo	string	非必须 渠道优惠金额 单位：分
     */
    public function orderQueryOrig($data)
    {
        return $this->request('tgPosp/services/payApi/orderQuery', [
            'account' => $this->account,
            ...$data,
        ]);
    }

    /**
     * 全额退款
     * @param string $orderNo
     * @param string $tgOrderId
     * @return array
     * status	number	必须 100:成功，101：失败	
     * message	string	必须 信息描述	
     * account	string	必须 聚合账户	
     * upOrderId	string	必须 通莞订单号	
     * lowOrderId	string	必须 下游订单号	
     * state	string	必须 0：成功，1：失败，2：已撤销 5：已退款申请成功 6：已转入部分退款申请成功	
     * sign	string	必须 签名
     */
    public function reverse($orderNo, $tgOrderId)
    {
        return $this->request('tgPosp/services/payApi/reverse', [
            'account' => $this->account,
            'lowOrderId' => $orderNo,
            'upOrderId' => $tgOrderId,
        ]);
    }

    /**
     * 部分退款
     * @param string $tgOrderId
     * @param string $refundMoney
     * @return array
     * status	number	必须 100:成功，101：失败	
     * message	string	必须 信息描述	
     * account	string	必须 聚合账户	
     * upOrderId	string	必须 通莞订单号	
     * lowOrderId	string	必须 下游订单号	
     * state	string	必须 0：成功，1：失败，2：已撤销 5：已退款申请成功 6：已转入部分退款申请成功	
     * sign	string	必须 签名
     */
    public function refund($tgOrderId, $refundMoney)
    {
        return $this->request('tgPosp/services/payApi/refund', [
            'account' => $this->account,
            'upOrderId' => $tgOrderId,
            'refundMoney' => $refundMoney,
        ]);
    }

    /**
     * 查询退款订单
     * @param string $refoundNo 下游退款订单号(lowRefundNo)
     * @return array
     * status	number	必须 100:成功，101：失败	
     * message	string	必须 信息描述	
     * orderId	string	必须 通莞订单号	
     * lowRefundNo	string	必须 下游退款订单号	
     * refundNo	string	必须 通莞退款订单号	
     * state	string	必须 0：支付成功，1：支付失败，2：已撤销 5：已退款申请成功 6：已转入部分退款申请成功	
     * sign	string	必须 签名	
     * refundTime	string	必须 退款时间	
     * refundState	string	必须 0：退款成功，1：退款失败，2退款中	
     * refundMoney	string	必须 退款金额	
     * remark	string	必须 备注	
     * channelId	string	必须 渠道ID
     */
    public function refundQuery($refundNo)
    {
        return $this->request('tgPosp/services/payApi/queryRefund', [
            'account' => $this->account,
            'refundNo' => $refundNo,
        ]);
    }

    /**
     * 关闭订单
     * @param string $orderNo
     * @param string $tgOrderId
     * @return array
     * status	number	必须 100:成功，101：失败	
     * message	string	必须 信息描述	
     * account	string	必须 聚合账号	
     * upOrderId	string	必须 通莞订单号	
     * sign	string	必须 签名
     */
    public function closeOrder($orderNo, $tgOrderId)
    {
        return $this->request('tgPosp/services/payApi/closeTradeOrder', [
            'account' => $this->account,
            'lowOrderId' => $orderNo,
            'upOrderId' => $tgOrderId,
        ]);
    }

    public function request($url, $data)
    {
        $data = $this->signData($data);


        $result = $this->http_post($this->baseURL . $url, json_encode($data), 1, ['Content-Type: application/json']);
        if ($resultData = @json_decode($result, true)) {
            return $resultData;
        }
        Log::error('tgpay: ' . var_export($data, true) . "\n" . $result);
        return $result;
    }

    public function verify($data)
    {
        if (!isset($data['sign'])) return false;
        $sign = $data['sign'];
        unset($data['sign']);
        $newSign = $this->sign($data);

        if ($sign == $newSign) {
            return true;
        } else {
            return false;
        }
    }
}
