<?php

namespace app\common\core;

use think\Db;
use shirne\third\KdExpress;

class BaseOrderModel extends BaseModel
{
    protected $pk = 'order_id';
    protected $padlen = 4;

    protected $lastInsertStatus = 0;
    protected function setInsertStatus($status)
    {
        $this->lastInsertStatus = $status;
    }
    public function getInsertStatus()
    {
        return $this->lastInsertStatus;
    }

    protected function orderno_sufix()
    {
        $key = 'order_no_' . strtolower(str_replace(['/', '\\'], '_', static::class));
        $maxid = cache($key);
        if (empty($maxid)) {
            $maxid = $this->field('max(order_id) as maxid')->find();
            $maxid = $maxid['maxid'];
        }
        if (empty($maxid)) $maxid = 0;
        $maxid++;
        cache($key, $maxid);
        return $this->pad_orderid($maxid, $this->padlen);
    }

    protected function create_no()
    {

        return date('YmdHis') . $this->orderno_sufix();
    }
    private function pad_orderid($id, $len = 3)
    {
        $strlen = strlen($id);
        return $strlen < $len ? str_pad($id, $len, '0', STR_PAD_LEFT) : substr($id, $strlen - $len);
    }

    protected function beforeStatus($data)
    {
        $data = parent::beforeStatus($data);
        if ($data['status'] == 1) {
            if (!isset($data['pay_time'])) {
                $data['pay_time'] = time();
            }
        } elseif ($data['status'] == 2) {
            if (!isset($data['deliver_time'])) {
                $data['deliver_time'] = time();
            }
        } elseif ($data['status'] == 3) {
            if (!isset($data['confirm_time'])) {
                $data['confirm_time'] = time();
            }
        } elseif ($data['status'] == 4) {
            if (!isset($data['comment_time'])) {
                $data['comment_time'] = time();
            }
        } elseif ($data['status'] < -2) {
            if (!isset($data['refund_time'])) {
                $data['refund_time'] = time();
            }
        } elseif ($data['status'] < 0) {
            if (!isset($data['cancel_time'])) {
                $data['cancel_time'] = time();
            }
        }
        return $data;
    }

    protected static function transkey($keywords)
    {
        $maps = [
            'order_no' => ['单号', '订单号', '订单编号', '订单号码'],
            'amount' => ['待付金额', '订单金额', '订单总价', '支付金额'],
            'goods' => ['商品详情', '物品名称', '商品名', '商品名称', '物品详情', '订单详情', '产品名称'],
            'count' => ['数量', '商品数量'],
            'pay_notice' => ['支付提醒'],
            'status' => ['订单状态', '状态'],
            'date' => ['变更时间'],
            'create_date' => ['下单时间', '购买时间'],
            'pay_date' => ['支付时间', '付款时间'],
            'express' => ['快递公司', '物流公司'],
            'express_no' => ['快递单号'],
            'deliver_date' => ['发货时间'],
            'confirm_date' => ['确认时间', '完成时间'],
            'receive_name' => ['姓名'],
            'receive_address' => ['地址'],
            'reason' => ['取消原因']
        ];
        if (!is_array($keywords)) {
            $keywords = explode('、', $keywords);
        }
        foreach ($keywords as $idx => $keyword) {
            foreach ($maps as $key => $words) {
                if (in_array($keyword, $words)) {
                    $keywords[$idx] = $key;
                    break;
                }
            }
        }
        return $keywords;
    }

    public function onPayResult($paytype, $paytime, $payamount)
    {
        return false;
    }

    /**
     * @param bool $force
     * @return array
     */
    public function fetchExpress($force = false)
    {

        $data = [];
        $expsetting = [
            'appid' => getSetting('kd_userid'),
            'appsecret' => getSetting('kd_apikey')
        ];
        if (empty($expsetting['appid']) || empty($expsetting['appsecret'])) {
            return $data;
        }
        if (!empty($this->express_no) && !empty($this->express_code)) {
            $cacheData = Db::name('expressCache')->where('express_code', $this->express_code)
                ->where('express_no', $this->express_no)->find();
            if (empty($cacheData) || $force || $cacheData['update_time'] < time() - 3600) {
                $express = new KdExpress($expsetting);
                $data = $express->QueryExpressTraces($this->express_code, $this->express_no);
                if (!empty($data)) {
                    $newData = ['data' => json_encode($data, JSON_UNESCAPED_UNICODE)];
                    if (empty($cacheData)) {
                        $newData['express_code'] = $this->express_code;
                        $newData['express_no'] = $this->express_no;
                        $newData['create_time'] = $newData['update_time'] = time();
                        Db::name('expressCache')->insert($newData);
                    } else {
                        $newData['update_time'] = time();
                        Db::name('expressCache')->where('id', $cacheData['id'])->update($newData);
                    }
                } else {
                    $data = [];
                }
            } elseif (!empty($cacheData['data'])) {
                $data = json_decode($cacheData['data'], true);
                if (is_string($data)) {
                    $data = json_decode($data, true);
                }
            }
        }
        return $data;
    }
}
