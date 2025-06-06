<?php

namespace app\api\controller\credit;

use app\api\controller\AuthedController;
use modules\credit\model\CreditOrderModel;
use app\common\validate\OrderValidate;
use think\Db;
use think\response\Json;

/**
 * 积分订单操作
 * Class OrderController
 * @package app\api\Controller
 */
class OrderController extends AuthedController
{
    /**
     * 初始化订单信息
     * @param array $goods 需要购买的商品列表，每个item包含id 和count,count默认1
     * @return Json 
     */
    public function prepare($goods)
    {

        $ids = array_column($goods, 'id');
        $counts = array_column($goods, 'count', 'id');
        $goodsData = Db::name('Goods')->field(['id', 'title', 'unit', 'price', 'market_price', 'image', 'levels', 'limit', 'storage', 'type'])
            ->whereIn('id', idArr($ids))
            ->select();

        foreach ($goodsData as &$goods) {
            if (!empty($goods['levels'])) {
                if (!in_array($this->user['level_id'], $goods['levels'])) {
                    $this->error('您当前会员组不允许购买商品[' . $goods['title'] . ']');
                }
            }
            $goods['count'] = empty($counts[$goods['id']]) ? 1 : $counts[$goods['id']];
        }
        unset($goods);

        return $this->response([
            'goods' => $goodsData,
            'address' => Db::name('MemberAddress')->where('member_id', $this->user['id'])->order('is_default DESC')->find(),
            'express' => [
                'fee' => 0,
                'title' => '快递免邮'
            ]
        ]);
    }

    /**
     * 确认下单
     * @param array $goods 商品信息，每个包含sku_id和count count默认为1
     * @param int $address_id 收货地址id
     * @param string $pay_type 支付类型
     * @param string $remark 订单备注
     * @param string $form_id 小程序中下单可获取到form_id 用以发送模板消息
     * @return mixed 
     */
    public function confirm($goods)
    {
        if (empty($this->config['credit_close'])) $this->error($this->config['credit_close_desc']);
        if (empty($goods)) $this->error('未选择下单商品');
        $goods_ids = array_column($goods, 'id');
        $goodsData = Db::view('Goods', '*')
            ->whereIn('Goods.id', idArr($goods_ids))
            ->select();
        $counts = array_index($goods, 'id,count');
        foreach ($goodsData as $k => &$item) {
            if (isset($counts[$item['id']])) {
                $item['count'] = $counts[$item['id']];
            } else {
                $item['count'] = 1;
            }
            if (!empty($item['levels'])) {
                $levels = json_decode($item['levels'], true);
                if (!in_array($this->user['level_id'], $levels)) {
                    $this->error('您当前会员组不允许购买商品[' . $item['title'] . ']');
                }
            }
        }
        unset($item);


        $total_price = 0;
        foreach ($goodsData as $item) {
            $total_price += $item['price'] * $item['count'];
        }
        //todo 邮费模板

        if ($total_price != $this->request->param('total_price')) {
            $this->error('下单商品价格已变动');
        }

        $data = $this->request->only('address_id,pay_type,remark,form_id', 'put');

        $validate = new OrderValidate();
        if (!$validate->check($data)) {
            $this->error($validate->getError());
        } else {
            $address = Db::name('MemberAddress')->where('member_id', $this->user['id'])
                ->where('address_id', $data['address_id'])->find();
            $balancepay = 0;
            if (!empty($data['pay_type'])) {
                if ($data['pay_type'] == 'balance' || $data['pay_type'] == 'money') $balancepay = 1;

                if (!$balancepay) {
                    $this->error('支付方式错误');
                }
            }
            if ($balancepay) {
                $secpassword = $this->request->param('secpassword');
                if (empty($secpassword)) {
                    $this->error('请填写安全密码');
                }
                if (!compare_secpassword($this->user, $secpassword)) {
                    $this->error('安全密码错误');
                }
            }
            $paycredit = true;
            $orderModel = new CreditOrderModel();
            $result = $orderModel->makeOrder($this->user, $goodsData, $address, $paycredit, $data['remark'], $balancepay);
            if ($result) {
                if ($balancepay || $paycredit === true) {
                    return $this->response(['order_id' => $result, 'payed' => 1], 1, '下单成功');
                } else {
                    $method = isset($data['pay_type']) ? ($data['pay_type'] . 'pay') : '';
                    if ($method && method_exists($this, $method)) {
                        return $this->$method($result);
                    } else {
                        return $this->response(['order_id' => $result], 1, '下单成功，请尽快支付');
                    }
                }
            } else {
                $this->error($orderModel->getError() ?: '下单失败');
            }
        }
    }
}
