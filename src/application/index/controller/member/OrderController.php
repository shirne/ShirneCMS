<?php

namespace app\index\controller\member;


use app\common\model\OrderModel;
use think\Db;
use think\Response;

/**
 * 订单管理
 * Class OrderController
 * @package app\index\controller\member
 */
class OrderController extends BaseController
{

    /**
     * 订单管理
     * @param int $status
     * @return Response
     */
    public function index($status = 0)
    {
        //
        $model = Db::name('Order')->where('member_id', $this->userid)
            ->where('delete_time', 0);
        if ($status > 0) {
            $model->where('status', $status - 1);
        }
        $orders = $model->order('status ASC,create_time DESC')->paginate();
        if (!empty($orders) && !$orders->isEmpty()) {
            $order_ids = array_column($orders->items(), 'order_id');
            $products = Db::view('OrderProduct', '*')
                ->view('Product', ['id' => 'orig_product_id', 'update_time' => 'orig_product_update'], 'OrderProduct.product_id=Product.id', 'LEFT')
                ->view('ProductSku', ['sku_id' => 'orig_sku_id', 'price' => 'orig_product_price'], 'ProductSku.sku_id=OrderProduct.sku_id', 'LEFT')
                ->whereIn('OrderProduct.order_id', $order_ids)
                ->select();
            $products = array_index($products, 'order_id', true);
            $orders->each(function ($item) use ($products) {
                $item['products'] = isset($products[$item['order_id']]) ? $products[$item['order_id']] : [];
                return $item;
            });
        }

        $countlist = Db::name('Order')->where('member_id', $this->userid)
            ->group('status')->field('status,count(order_id) as order_count')->paginate(10);
        $counts = [0, 0, 0, 0, 0, 0, 0];
        foreach ($countlist as $row) {
            $counts[$row['status']] = $row['order_count'];
        }

        $this->assign('counts', $counts);
        $this->assign('orders', $orders);
        $this->assign('status', $status);
        $this->assign('page', $orders->render());
        return $this->fetch();
    }

    public function detail($id)
    {
        $id = intval($id);
        $order = OrderModel::get($id);
        if (empty($order) || $order['member_id'] != $this->userid || $order['delete_time'] > 0) {
            $this->error('订单不存在或已删除', aurl('index/member.order/index'));
        }
        $this->assign('order', $order);
        $this->assign('products', Db::name('OrderProduct')->where('order_id', $id)->select());
        return $this->fetch();
    }


    public function cancel($id, $reason = '')
    {
        $order = OrderModel::get(intval($id));
        if (empty($order) || $order['member_id'] != $this->userid || $order['delete_time'] > 0) {
            $this->error('订单不存在或已删除', 0);
        }
        if ($order['status'] != 0) {
            $this->error('订单状态错误', 0);
        }
        $success = $order->updateStatus(['status' => -2, 'reason' => $reason]);
        if ($success) {
            user_log($this->userid, 'cancelorder', 1, '取消订单 ' . $id);
            $this->success('订单已取消');
        } else {
            $this->error('取消失败');
        }
    }

    public function refund($id, $reason = '')
    {
        $order = OrderModel::get(intval($id));
        if (empty($order) || $order['member_id'] != $this->userid || $order['delete_time'] > 0) {
            $this->error('订单不存在或已删除', 0);
        }
        if ($order['status'] < 1) {
            $this->error('订单状态错误', 0);
        }

        //退款
        $success = $order->updateStatus(['status' => -3, 'reason' => $reason]);
        if ($success) {
            user_log($this->userid, 'refundorder', 1, '申请退款 ' . $id);
            $this->success('订单已申请退款');
        } else {
            $this->error('取消失败');
        }
    }


    /**
     * 删除订单
     * @param $id
     */
    public function delete($id)
    {
        $model = Db::name('order');
        if (empty($model) || $model['member_id'] != $this->userid || $model['status'] > -1 || $model['status'] == -3) {
            $this->error('订单当前状态不可删除', 0);
        }
        $result = $model->where('member_id', $this->userid)->whereIn("order_id", idArr($id))
            ->useSoftDelete('delete_time', time())->delete();
        if ($result) {
            //Db::name('orderProduct')->whereIn("order_id",idArr($id))->delete();
            user_log($this->userid, 'deleteorder', 1, '删除订单 ' . $id);
            $this->success("删除成功", aurl('index/member.order/index'));
        } else {
            $this->error("删除失败");
        }
    }

    /**
     * 订单确认
     * @param $id int
     */
    public function confirm($id)
    {
        $model = OrderModel::get(intval($id));

        if (!$model['isaudit']) {
            $this->error('订单尚未审核');
        }

        if (empty($model) || $model['member_id'] != $this->userid) {
            $this->error('订单不存在');
        }
        $model->updateStatus(array('status' => 3, 'confirm_time' => time()));
        $this->success('确认完成');
    }
}
