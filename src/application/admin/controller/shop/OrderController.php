<?php

namespace app\admin\controller\shop;

use app\admin\controller\BaseController;
use app\common\model\OrderModel;
use app\common\model\OrderProductModel;
use app\common\model\OrderRefundModel;
use app\common\model\PayOrderModel;
use shirne\excel\Excel;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use think\Db;

/**
 * 订单管理
 * Class OrderController
 * @package app\admin\controller\shop
 */
class OrderController extends BaseController
{
    /**
     * 订单列表
     * @param string $keyword
     * @param string $start_date
     * @param string $end_date
     * @param string $status
     * @param string $audit
     * @return mixed|\think\response\Redirect
     */
    public function index($keyword = '', $start_date = '', $end_date = '', $status = '', $audit = '')
    {
        if ($this->request->isPost()) {
            return redirect(url('', ['status' => $status, 'start_date' => $start_date, 'end_date' => $end_date, 'audit' => $audit, 'keyword' => base64url_encode($keyword)]));
        }
        $keyword = empty($keyword) ? "" : base64url_decode($keyword);
        $model = Db::view('order', '*')
            ->view('member', ['username', 'realname', 'nickname', 'avatar', 'level_id'], 'member.id=order.member_id', 'LEFT')
            ->where('order.delete_time', 0);

        if (!empty($keyword)) {
            $model->whereLike('order.order_no|member.username|member.nickname|member.realname|order.receive_name|order.mobile', "%$keyword%");
        }
        if ($status !== '') {
            $model->where('order.status', $status);
        }
        if ($audit !== '') {
            $model->where('order.isaudit', $audit);
        }
        if ($start_date !== '') {
            if ($end_date !== '') {
                $model->whereBetween('order.create_time', [strtotime($start_date), strtotime($end_date . ' 23:59:59')]);
            } else {
                $model->where('order.create_time', 'GT', strtotime($start_date));
            }
        } else {
            if ($end_date !== '') {
                $model->where('order.create_time', 'LT', strtotime($end_date . ' 23:59:59'));
            }
        }

        $lists = $model->where('order.delete_time', 0)->order(Db::raw('if(order.status>-1,order.status,3) ASC,order.create_time DESC'))->paginate(15);
        if (!$lists->isEmpty()) {
            $orderids = array_column($lists->items(), 'order_id');
            $prodata = Db::name('OrderProduct')->whereIn('order_id',  $orderids)->select();
            $products = array_index($prodata, 'order_id', true);
            $lists->each(function ($item) use ($products) {
                if (isset($products[$item['order_id']])) {
                    $item['products'] = $products[$item['order_id']];
                } else {
                    $item['products'] = [];
                }
                return $item;
            });
        }

        $this->assign('keyword', $keyword);
        $this->assign('start_date', $start_date);
        $this->assign('end_date', $end_date);
        $this->assign('status', $status);
        $this->assign('orderids', empty($orderids) ? 0 : implode(',', $orderids));
        $this->assign('audit', $audit);
        $this->assign('expresscodes', config('express.'));
        $this->assign('lists', $lists);
        $this->assign('levels', getMemberLevels());
        $this->assign('page', $lists->render());
        return $this->fetch();
    }

    /**
     * 导出订单
     * @param $order_ids
     * @param string $keyword
     * @param string $start_date
     * @param string $end_date
     * @param string $status
     * @param string $audit
     * @param string $mode
     */
    public function export($order_ids = '', $keyword = '', $start_date = '', $end_date = '', $status = '', $audit = '', $mode = '')
    {
        $keyword = empty($keyword) ? "" : base64_decode($keyword);
        $model = Db::view('order', '*')
            ->view('member', ['username', 'realname', 'nickname', 'avatar', 'level_id'], 'member.id=order.member_id', 'LEFT')
            ->where('order.delete_time', 0);
        if (empty($order_ids)) {
            if (!empty($keyword)) {
                $model->whereLike('order.order_no|member.username|member.realname|order.receive_name|order.mobile', "%$keyword%");
            }
            if ($status !== '') {
                $model->where('order.status', $status);
            }
            if ($audit !== '') {
                $model->where('order.isaudit', $audit);
            }

            if ($start_date !== '') {
                if ($end_date !== '') {
                    $model->whereBetween('order.create_time', [strtotime($start_date), strtotime($end_date . ' 23:59:59')]);
                } else {
                    $model->where('order.create_time', 'GT', strtotime($start_date));
                }
            } else {
                if ($end_date !== '') {
                    $model->where('order.create_time', 'LT', strtotime($end_date . ' 23:59:59'));
                }
            }
        } elseif ($order_ids == 'status') {
            $model->where('status', 1);
        } else {
            $model->whereIn('order.order_id', idArr($order_ids));
        }


        $rows = $model->order('order.create_time DESC')->select();
        if (empty($rows)) {
            $this->error('没有选择要导出的项目');
        }

        if ($mode == 'express') {
            $this->exportForExpress($rows);
        } else {
            $this->exportData($rows);
        }
    }

    private function exportData($rows)
    {
        $excel = new Excel();
        $excel->setHeader(array(
            '编号',
            '状态',
            '时间',
            '会员ID',
            '会员账号',
            '购买产品',
            '购买价格',
            '收货人',
            '电话',
            '省',
            '市',
            '区',
            '地址'
        ));
        $excel->setColumnType('A', DataType::TYPE_STRING);
        $excel->setColumnType('D', DataType::TYPE_STRING);
        $excel->setColumnType('I', DataType::TYPE_STRING);

        foreach ($rows as $row) {
            $prodata = Db::name('OrderProduct')->where('order_id', $row['order_id'])->find();
            $username = $row['username'];
            if (strpos($username, '#') === 0) {
                $username = filter_emoji($row['nickname'], '?');
            }
            $excel->addRow(array(
                $row['order_no'],
                order_status($row['status'], false),
                date('Y/m/d H:i:s', $row['create_time']),
                $row['member_id'],
                $username,
                $prodata['product_title'],
                $row['payamount'],
                $row['receive_name'],
                $row['mobile'],
                $row['province'],
                $row['city'],
                $row['area'],
                $row['address']
            ));
        }

        $excel->output(date('Y-m-d-H-i') . '-订单导出[' . count($rows) . '条]');
    }

    private function exportForExpress($rows)
    {
        $excel = new Excel();
        $excel->setHeader(array(
            '',
            '收件信息',
            '',
            '',
            '物品信息',
            '',
            '备注'
        ));
        $excel->getSheet()->setMergeCells(['B1:D1', 'E1:F1']);
        $excel->setHeader(array(
            '编号',
            '收件人姓名',
            '收件人联系方式',
            '收件地址',
            '物品类',
            '物品详情',
            '备注信息'
        ));
        $excel->setColumnType('A', DataType::TYPE_STRING);
        $excel->setColumnType('C', DataType::TYPE_STRING);

        foreach ($rows as $row) {
            $prodata = Db::name('OrderProduct')->where('order_id', $row['order_id'])->select();
            $prods = [];
            foreach ($prodata as $prod) {
                if (count($prods) > 2) {
                    $prods[2] .= '等';
                    break;
                }
                $prods[] = $prod['product_title'];
            }
            $excel->addRow(array(
                $row['order_no'],
                $row['receive_name'],
                $row['mobile'],
                $row['province'] . $row['city'] . $row['area'] . $row['address'],
                '食品',
                implode('、', $prods),
                ''
            ));
        }

        $excel->output(date('Y-m-d-H-i') . '-订单发货[' . count($rows) . '条]');
    }

    /**
     * 订单详情
     * @param $id
     * @return \think\Response
     */
    public function detail($id)
    {
        $model = Db::name('Order')->where('order_id', $id)->find();
        if (empty($model)) $this->error('订单不存在');
        $member = Db::name('Member')->find($model['member_id']);
        $products = OrderProductModel::where('order_id',  $id)->select();
        $payorders = PayOrderModel::filterTypeAndId('order', $id)->select();
        $refunds = [];
        if ($model['status'] < -1) {
            $refunds = OrderRefundModel::where('order_id', $id)->select();
        }
        $this->assign('model', $model);
        $this->assign('member', $member);
        $this->assign('products', $products);
        $this->assign('payorders', $payorders);
        $this->assign('expresscodes', config('express.'));
        $this->assign('refunds', $refunds);
        return $this->fetch();
    }

    public function setstatus($ids)
    {
        $ids = idArr($ids);
        $status = $this->request->param('status');

        $orders = OrderModel::whereIn('order_id', $ids)->select();
        if (empty($orders)) {
            $this->error('参数无效');
        }
        $errors = [];
        foreach ($orders as $order) {
            if ($order['status'] < 1) {
                $errors[] = '订单[' . $order['order_id'] . ']状态错误';
                continue;
            }
            if ($order['status'] < $status) {
                for ($i = $order['status'] + 1; $i <= $status; $i++) {
                    $order->updateStatus($i);
                }
            }
        }

        $this->success(implode("\n", $errors) . "\n操作完成");
    }

    public function setcancel($id)
    {
        $id = intval($id);
        $order = OrderModel::get($id);
        if (empty($id) || empty($order)) {
            $this->error('订单不存在');
        }
        if ($order['status'] > 0) {
            $this->error('订单不可取消');
        }
        $order->updateStatus(['status' => -1]);
        user_log($this->mid, 'cancelorder', 1, '取消订单 ' . $id, 'manager');
        $this->success('操作成功');
    }
    public function setpayed($id)
    {
        $id = intval($id);
        $order = OrderModel::get($id);
        if (empty($id) || empty($order)) {
            $this->error('订单不存在');
        }
        if ($order['status'] < 0) {
            $this->error('订单已失效');
        }
        if ($order['status'] >= 1) {
            $this->error('订单已支付');
        }
        $paytype = $this->request->post('paytype');
        if ($paytype == 'balance' || $paytype == 'reward') {
            $amount = ($order['payamount'] - $order['payedamount']) * 100;
            $debit = money_log($order['member_id'], -$amount, "订单支付", 'consume', 0, $paytype == 'balance' ? 'money' : $paytype);
            if (!$debit) {
                $this->error('用户' . lang(lcfirst($paytype)) . '不足');
            }
        } else {
            $paytype = 'offline';
        }
        $order->updateStatus(['status' => 1, 'pay_type' => $paytype]);
        user_log($this->mid, 'orderpay', 1, '订单支付 ' . $id, 'manager');
        $this->success('操作成功');
    }
    public function setdelivery($id)
    {
        $id = intval($id);
        $order = OrderModel::get($id);
        if (empty($id) || empty($order)) {
            $this->error('订单不存在');
        }
        if ($order['status'] < 0) {
            $this->error('订单已失效');
        }
        if ($order['status'] < 1) {
            $this->error('订单未支付');
        }
        if ($order['status'] > 2) {
            $this->error('订单已发货');
        }
        $express_no = $this->request->post('express_no');
        $express_code = $this->request->post('express_code');

        $order->updateStatus([
            'status' => 2,
            'express_no' => $express_no,
            'express_code' => $express_code
        ]);
        user_log($this->mid, 'orderdelivery', 1, '订单发货 ' . $id, 'manager');
        $this->success('操作成功');
    }
    public function setreceive($id)
    {
        $id = intval($id);
        $order = OrderModel::get($id);
        if (empty($id) || empty($order)) {
            $this->error('订单不存在');
        }
        if ($order['status'] < 0) {
            $this->error('订单已失效');
        }
        if ($order['status'] < 2) {
            $this->error('订单未发货');
        }
        if ($order['status'] >= 3) {
            $this->error('订单已完成');
        }
        $order->updateStatus(['status' => 3]);
        user_log($this->mid, 'orderconfirm', 1, '订单确认 ' . $id, 'manager');
        $this->success('操作成功');
    }

    public function setcomplete($id)
    {
        $id = intval($id);
        $order = OrderModel::get($id);
        if (empty($id) || empty($order)) {
            $this->error('订单不存在');
        }
        if ($order['status'] < 0) {
            $this->error('订单已失效');
        }
        if ($order['status'] < 3) {
            $this->error('订单未收货');
        }
        if ($order['status'] >= 4) {
            $this->error('订单已完成');
        }
        $order->updateStatus(['status' => 4]);
        user_log($this->mid, 'ordercomplete', 1, '订单完成 ' . $id, 'manager');
        $this->success('操作成功');
    }

    /**
     * 订单进度修改
     * @param $id
     */
    public function status($id)
    {
        $this->error('操作已失效');
    }



    /**
     * 改价
     * @param $id
     * @param $price
     * @throws \Exception
     */
    public function reprice($id, $price)
    {
        $id = intval($id);
        $order = OrderModel::get($id);
        if (empty($id) || empty($order)) {
            $this->error('订单不存在');
        }
        if ($order['status'] != 0) {
            $this->error('订单当前状态不可改价');
        }
        $price = $this->request->post('price');

        $data = array(
            'payamount' => round(floatval($price), 2)
        );

        $order->save($data);
        user_log($this->mid, 'repriceorder', 1, '订单改价 ' . $id . ' ' . $price, 'manager');
        $this->success('操作成功');
    }

    /**
     * 支付订单查询
     * @param $id
     */
    public function paystatus($id)
    {
        $id = intval($id);
        $order = OrderModel::get($id);
        if (empty($id) || empty($order)) {
            $this->error('订单不存在');
        }
        if ($order['status'] != 0) {
            $this->error('订单当前状态非待支付');
        }
        $payorders = PayOrderModel::filterTypeAndId('order', $id)->select();
        if (empty($payorders)) {
            $this->error('该订单没有在线支付记录');
        }
        foreach ($payorders as $porder) {
            $porder->checkStatus();
        }

        $this->success('操作成功', null, ['lists' => $payorders]);
    }

    /**
     * 支付状态查询 todo
     * @param $payid
     */
    public function payquery($payid)
    {
        $payid = intval($payid);
        $payorder = PayOrderModel::get($payid);
        if (empty($payorder)) {
            $this->error('支付订单不存在');
        }
        $result = $payorder->checkStatus();
        if ($result) {
            if ($payorder->getErrNo()) {
                $this->error('查询成功：' . $payorder->getError());
            } else {
                $this->success('订单已支付');
            }
        } else {
            $this->error($payorder->getError());
        }
    }

    /**
     * 审核订单
     * @param $id
     */
    public function audit($id)
    {
        $id = intval($id);
        $order = OrderModel::get($id);
        if (empty($id) || empty($order)) {
            $this->error('订单不存在');
        }
        $audit = $this->request->post('status/d');

        $order->audit();
        user_log($this->mid, 'auditorder', 1, '审核订单 ' . $id . ' ' . $audit, 'manager');
        $this->success('操作成功');
    }

    /**
     * 删除订单
     * @param $id
     */
    public function delete($id)
    {
        $model = Db::name('order');
        $result = $model->whereIn("order_id", idArr($id))->useSoftDelete('delete_time', time())->delete();
        if ($result) {
            //Db::name('orderProduct')->whereIn("order_id",idArr($id))->delete();
            user_log($this->mid, 'deleteorder', 1, '删除订单 ' . $id, 'manager');
            $this->success(lang('Delete success!'), url('shop.order/index'));
        } else {
            $this->error(lang('Delete failed!'));
        }
    }

    public function refundcancel($id, $reason = '')
    {
        $id = intval($id);
        $refund = OrderRefundModel::where('id', $id)->find();
        if (empty($refund) || $refund['status'] != 0) {
            $this->error('该投诉已处理过了');
        }
        $refund->save([
            'status' => -1,
            'reply' => $reason
        ]);
        $order = OrderModel::where('order_id', $refund['order_id'])->find();

        user_log($this->mid, 'orderrefundcancel', 1, '拒绝退款单 ' . $id, 'manager');
        $this->success('处理成功');
    }

    public function refundallow($id)
    {
        $id = intval($id);
        $refund = OrderRefundModel::where('id', $id)->find();
        if (empty($refund) || $refund['status'] != 0) {
            $this->error('该投诉已处理过了');
        }
        $order = OrderModel::where('order_id', $refund['order_id'])->find();
        if (empty($order) || $order['status'] < 1) {
            $this->error('订单状态错误');
        }

        if ($order['pay_type'] == 'balance') {
            $loged = money_log($order['member_id'], $order['payamount'], "订单退款", 'refund', 0, 'money');
            if ($loged) {
                $refund->save([
                    'status' => 1,
                    'reply' => 'success'
                ]);
            } else {
                $this->success('退款失败');
            }
        } else {
            $refunded = PayOrderModel::refund($refund['order_id'], 'order', $refund['type']);
            if ($refunded) {
                $refund->save([
                    'status' => 1,
                    'reply' => 'process'
                ]);
            } else {
                $this->success('退款申请失败');
            }
        }
        user_log($this->mid, 'orderrefundallow', 1, '通过退款单 ' . $id, 'manager');
        $this->success('处理成功');
    }
}
