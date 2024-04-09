<?php

namespace app\api\controller;

use app\api\handler\WechatMiniHandler;
use app\api\handler\WechatOfficialHandler;
use app\api\handler\WechatPlatformHandler;
use app\common\model\PayOrderModel;
use app\common\model\PayOrderRefundModel;
use app\common\model\WechatModel;
use EasyWeChat\BasicService\Application;
use EasyWeChat\Factory;
use think\Controller;
use think\Db;
use think\facade\Log;

/**
 * 微信消息接口入口
 * Class WeChatController
 * @package app\api\controller
 */
class WechatController extends Controller
{

    protected $config = array();

    /**
     * @var Application
     */

    public function initialize()
    {
        parent::initialize();
        $this->config = getSettings();
    }

    protected function getAccount($hash = '')
    {
        if (!empty($hash)) {
            $account = Db::name('wechat')->where('hash', $hash)->find();
            if (empty($account)) {
                Log::warning('公众号[' . $hash . ']不存在');
            }
        } else {
            $account = Db::name('wechat')->where('is_default', 1)->where('type', 'wechat')->find();
            if (empty($account)) {
                Log::warning('没有设置默认公众号');
            }
        }
        if (empty($account)) {
            exit;
        }
        return $account;
    }

    /**
     * 微信通知入口
     * @param string $hash 
     * @return never 
     */
    public function index($hash = '')
    {
        Log::info('收到消息' . $hash);
        $account = $this->getAccount($hash);

        $app = WechatModel::createApp($account);
        $app['account'] = $account;

        try {
            switch ($account['account_type']) {
                case 'wechat':
                case 'subscribe':
                case 'service':
                    $app->server->push(WechatOfficialHandler::class);
                    break;
                case 'miniprogram':
                case 'minigame':
                    $app->server->push(WechatMiniHandler::class);
                    break;
                case 'platform':
                    $app->server->push(WechatPlatformHandler::class);
                    break;
                case 'work':
                case 'openwork':
                case 'micromerchant':
                default:
                    Log::warning('不支持的公众号类型：' . $account['account_type']);
                    exit;
                    break;
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            exit;
        }


        try {
            $response = $app->server->serve();
        } catch (\Exception $e) {
            Log::error('消息回复错误:' . $e->getMessage());
            exit;
        }

        $response->send();
        exit;
    }

    /**
     * 退款通知入口
     * @param string $hash 
     * @return never 
     */
    public function refund($hash = '')
    {
        $account = $this->getAccount($hash);
        $config = WechatModel::to_pay_config($account);

        $app = Factory::payment($config);
        $response = $app->handleRefundedNotify(function ($message, $reqInfo, $fail) {
            Log::info(var_export($message, TRUE));

            $order = PayOrderRefundModel::where(['refund_no' => $message['out_refund_no']])->find();

            if (empty($order) || $order['pay_time'] > 0) {
                return true;
            }

            if ($message['return_code'] === 'SUCCESS') {
                // 退款是否成功
                if ($message['refund_status'] === 'SUCCESS') {
                    $data = [
                        'status' => 1,
                        'refund_time' => strtotime($message['success_time']),
                        'refund_result' => $message['refund_recv_accout'],
                        'update_time' => time(),
                    ];

                    // 退款失败
                } else {
                    $data = [
                        'status' => 0,
                        'refund_result' => $message['refund_status']
                    ];
                }
            } else {
                return $fail('通信失败，请稍后再通知我');
            }

            if (!empty($data)) {
                try {
                    $order->updateStatus($data);
                } catch (\Exception $e) {
                    Log::error($e->getMessage());
                    Log::error($e->getTraceAsString());
                }
            }

            return true;
        });
        $response->send();
        exit;
    }

    /**
     * 支付通知入口
     * @param string $hash 
     * @return never 
     */
    public function payresult($hash = '')
    {
        $account = $this->getAccount($hash);
        $config = WechatModel::to_pay_config($account);

        $app = Factory::payment($config);

        $response = $app->handlePaidNotify(function ($message, $fail) {
            // 记录日志
            Log::info(var_export($message, TRUE));

            /**
             * @var $order PayOrderModel
             */
            $order = PayOrderModel::where(['order_no' => $message['out_trade_no']])->find();

            if (empty($order) || $order['pay_time'] > 0) {
                return true;
            }


            if ($message['return_code'] === 'SUCCESS') {
                // 用户是否支付成功
                if ($message['result_code'] === 'SUCCESS') {
                    $data = [
                        'status' => 1,
                        'pay_time' => time(),
                        'pay_bill' => $message['transaction_id'],
                        'time_end' => $message['time_end']
                    ];

                    // 用户支付失败
                } elseif ($message['result_code'] === 'FAIL') {
                    $data = [
                        'status' => 0
                    ];
                }
            } else {
                return $fail('通信失败，请稍后再通知我');
            }

            if (!empty($data)) {
                try {
                    $order->updateStatus($data);
                } catch (\Exception $e) {
                    Log::error($e->getMessage());
                    Log::error($e->getTraceAsString());
                }
            }

            return true;
        });

        $response->send();
        exit;
    }

    /**
     * 扫码支付通知入口
     * @param string $hash 
     * @return never 
     */
    public function scanpay($hash = '')
    {
        $account = $this->getAccount($hash);
        $config = WechatModel::to_pay_config($account);

        $app = Factory::payment($config);

        $response = $app->handlePaidNotify(function ($message, $fail) {
            // 记录日志
            Log::info(var_export($message, TRUE));

            return true;
        });

        $response->send();
        exit;
    }
}
