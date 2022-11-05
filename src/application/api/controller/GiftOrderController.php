<?php

namespace app\api\controller;

use app\common\facade\MemberCartFacade;
use app\common\model\OrderModel;
use app\common\model\ProductModel;
use app\common\validate\OrderValidate;
use think\Db;
use think\response\Json;

/**
 * 礼包订单操作
 * Class GiftOrderController
 * @package app\api\Controller
 */
class GiftOrderController extends AuthedController
{
    /**
     * 初始化订单信息
     * @param array $goods 需要购买的商品列表，每个item包含id 和count,count默认1
     * @return Json 
     */
    public function prepare($goods)
    {
        if (empty($this->user['money'])) {
            $this->error('您的' . lang('Balance') . '不足');
        }
        $address = $this->request->param('address');
        $counts = array_column($goods, 'count', 'id');
        $products = ProductModel::getForOrder($counts);

        foreach ($products as $product) {
            if (!empty($product['levels'])) {
                if (!in_array($this->user['level_id'], $product['levels'])) {
                    $this->error('您当前会员组不允许购买商品[' . $product['product_title'] . ']');
                }
            }
        }
        unset($product);

        if (empty($address)) {
            $address = Db::name('MemberAddress')->where('member_id', $this->user['id'])->order('is_default DESC')->find();
        } elseif (!is_array($address)) {
            $address = Db::name('MemberAddress')->where('member_id', $this->user['id'])->where('address_id', $address)->order('is_default DESC')->find();
        }

        return $this->response([
            'products' => $products,
            'address' => $address,
            'express' => [
                'fee' => 0,
                'title' => '快递免邮'
            ]
        ]);
    }

    /**
     * 确认下单
     * @param string $from 下单来源，购物车或直接下单，购物车下单会移除下单成功的商品
     * @param array $goods 商品信息，每个包含sku_id和count count默认为1
     * @param int $address_id 收货地址id
     * @param string $pay_type 支付类型
     * @param string $remark 订单备注
     * @param string $form_id 小程序中下单可获取到form_id 用以发送模板消息
     * @return mixed 
     */
    public function confirm($from = 'gift')
    {
        if (empty($this->config['shop_close'])) $this->error($this->config['shop_close_desc']);
        $this->check_submit_rate();

        $order_skus = $this->request->param('products');
        if (empty($order_skus)) $this->error('未选择下单商品');
        $sku_ids = array_column($order_skus, 'sku_id');

        $skucounts = array_column($order_skus, 'count', 'sku_id');
        $products = ProductModel::getForOrder($skucounts);

        $products = array_column($products, NULL, 'sku_id');

        foreach ($order_skus as $k => $item) {
            if (!isset($products[$item['sku_id']])) {
                $this->error('部分商品已下架');
            }
            if ($products[$item['sku_id']]['cate_id'] != 3) {
                $this->error('非礼包专区商品');
            }
            $products[$item['sku_id']]['postage_area_id'] = $item['postage_area_id'];
            $order_skus[$k] = $products[$item['sku_id']];
        }

        //todo 邮费模板


        $data = $this->request->only('address_id,pay_type,remark,subscribe,total_price,total_postage', 'put');
        if (floatval($data['total_price']) < 1680) {
            $this->error('购买金额需满 1680 ');
        }

        $validate = new OrderValidate();
        if (!$validate->check($data)) {
            $this->error($validate->getError());
        } else {
            $address = Db::name('MemberAddress')->where('member_id', $this->user['id'])
                ->where('address_id', $data['address_id'])->find();
            $balancepay = 1;
            //$data['pay_type'] = 'money';

            // if($balancepay){
            //     $secpassword=$this->request->param('secpassword');
            //     if(empty($secpassword)){
            //         $this->error('请填写安全密码');
            //     }
            //     if(!compare_secpassword($this->user,$secpassword)){
            //         $this->error('安全密码错误');
            //     }
            // }

            $platform = $this->request->tokenData['platform'] ?: '';
            $appid = $this->request->tokenData['appid'] ?: '';

            $remark = [
                'remark' => $data['remark'],
                'platform' => $platform,
                'appid' => $appid,
                'total_price' => $data['total_price'],
                'total_postage' => $data['total_postage'],
            ];
            if (isset($data['subscribe'])) {
                $remark['subscribe'] = $data['subscribe'];
            }
            $orderModel = new OrderModel();
            try {
                $result = $orderModel->makeOrder($this->user, $order_skus, $address, $remark, $balancepay);
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
            if ($result) {

                if ($balancepay && $orderModel->getInsertStatus()) {
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
                $this->error('下单失败:' . $orderModel->getError());
            }
        }
    }
}
