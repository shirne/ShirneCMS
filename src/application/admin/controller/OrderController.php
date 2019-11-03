<?php

namespace app\admin\controller;

use app\common\model\OrderModel;
use app\common\model\PayOrderModel;
use shirne\excel\Excel;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use think\Db;
use think\Exception;

/**
 * 订单管理
 * Class OrderController
 * @package app\admin\controller
 */
class OrderController extends BaseController
{
    /**
     * 订单列表
     * @param string $key
     * @param string $status
     * @param string $audit
     * @return mixed|\think\response\Redirect
     */
    public function index($key='',$status='',$audit=''){
        if($this->request->isPost()){
            return redirect(url('',['status'=>$status,'audit'=>$audit,'key'=>base64_encode($key)]));
        }
        $key=empty($key)?"":base64_decode($key);
        $model=Db::view('order','*')
            ->view('member',['username','realname','nickname','avatar','level_id'],'member.id=order.member_id','LEFT')
            ->where('order.delete_time',0);

        if(!empty($key)){
            $model->whereLike('order.order_no|member.username|member.realname|order.recive_name|order.mobile',"%$key%");
        }
        if($status!==''){
            $model->where('order.status',$status);
        }
        if($audit!==''){
            $model->where('order.isaudit',$audit);
        }

        $lists=$model->where('order.delete_time',0)->order(Db::raw('if(order.status>-1,order.status,3) ASC,order.create_time DESC'))->paginate(15);
        if(!$lists->isEmpty()) {
            $orderids = array_column($lists->items(), 'order_id');
            $prodata = Db::name('OrderProduct')->where('order_id', 'in', $orderids)->select();
            $products=array_index($prodata,'order_id',true);
            $lists->each(function($item) use ($products){
                if(isset($products[$item['order_id']])){
                    $item['products']=$products[$item['order_id']];
                }else {
                    $item['products'] = [];
                }
                return $item;
            });
        }

        $this->assign('key',$key);
        $this->assign('status',$status);
        $this->assign('orderids',empty($orderids)?0:implode(',',$orderids));
        $this->assign('audit',$audit);
        $this->assign('expresscodes',config('express.'));
        $this->assign('lists',$lists);
        $this->assign('levels',getMemberLevels());
        $this->assign('page',$lists->render());
        return $this->fetch();
    }

    /**
     * 导出订单
     * @param $order_ids
     * @param string $key
     * @param string $status
     * @param string $audit
     */
    public function export($order_ids='',$key='',$status='',$audit=''){
        $key=empty($key)?"":base64_decode($key);
        $model=Db::view('order','*')
            ->view('member',['username','realname','avatar','level_id'],'member.id=order.member_id','LEFT')
            ->where('order.delete_time',0);
        if(empty($order_ids)){
            if(!empty($key)){
                $model->whereLike('order.order_no|member.username|member.realname|order.recive_name|order.mobile',"%$key%");
            }
            if($status!==''){
                $model->where('order.status',$status);
            }
            if($audit!==''){
                $model->where('order.isaudit',$audit);
            }
        }elseif($order_ids=='status') {
            $model->where('status',1);
        }else{
            $model->whereIn('order.order_id',idArr($order_ids));
        }


        $rows=$model->order('order.create_time DESC')->select();
        if(empty($rows)){
            $this->error('没有选择要导出的项目');
        }

        $excel=new Excel();
        $excel->setHeader(array(
            '编号','状态','时间','会员ID','会员账号','购买产品','购买价格','收货人','电话','省','市','区','地址'
        ));
        $excel->setColumnType('A',DataType::TYPE_STRING);
        $excel->setColumnType('D',DataType::TYPE_STRING);
        $excel->setColumnType('I',DataType::TYPE_STRING);

        foreach ($rows as $row){
            $prodata = Db::name('OrderProduct')->where('order_id', $row['order_id'])->find();
            $excel->addRow(array(
                $row['order_id'],order_status($row['status'],false),date('Y/m/d H:i:s',$row['create_time']),$row['member_id'],$row['username'],
                $prodata['product_title'],$row['payamount'],$row['recive_name'],$row['mobile'],$row['province'],$row['city'],$row['area'],$row['address']
            ));
        }

        $excel->output(date('Y-m-d-H-i').'-订单导出['.count($rows).'条]');
    }

    /**
     * 订单详情
     * @param $id
     * @return \think\Response
     */
    public function detail($id){
        $model=Db::name('Order')->where('order_id',$id)->find();
        if(empty($model))$this->error('订单不存在');
        $member=Db::name('Member')->find($model['member_id']);
        $products = Db::name('OrderProduct')->where('order_id',  $id)->select();
        $payorders = PayOrderModel::filterTypeAndId('order',$id)->select();
        $this->assign('model',$model);
        $this->assign('member',$member);
        $this->assign('products',$products);
        $this->assign('payorders',$payorders);
        $this->assign('expresscodes',config('express.'));
        return $this->fetch();
    }

    public function setcancel($id){
        $order = OrderModel::get($id);
        if(empty($id) || empty($order)){
            $this->error('订单不存在');
        }
        if($order['status'] > 0){
            $this->error('订单不可取消');
        }
        $order->updateStatus(['status'=>-1]);
        user_log($this->mid,'cancelorder',1,'取消订单 '.$id ,'manager');
        $this->success('操作成功');
    }
    public function setpayed($id){
        $order = OrderModel::get($id);
        if(empty($id) || empty($order)){
            $this->error('订单不存在');
        }
        if($order['status'] < 0){
            $this->error('订单已失效');
        }
        if($order['status'] >= 1){
            $this->error('订单已支付');
        }
        $paytype=$this->request->post('paytype');
        if($paytype == 'balance'){
            $debit = money_log($order['member_id'], -$order['payamount']*100, "订单支付", 'consume',0,'money');
            if(!$debit){
                $this->error('用户余额不足');
            }
        }else{
            $paytype = 'offline';
        }
        $order->updateStatus(['status'=>1,'pay_type'=>$paytype]);
        user_log($this->mid,'orderpay',1,'订单支付 '.$id ,'manager');
        $this->success('操作成功');
    }
    public function setdelivery($id){
        $order = OrderModel::get($id);
        if(empty($id) || empty($order)){
            $this->error('订单不存在');
        }
        if($order['status'] < 0){
            $this->error('订单已失效');
        }
        if($order['status'] < 1){
            $this->error('订单未支付');
        }
        if($order['status'] > 2){
            $this->error('订单已发货');
        }
        $express_no=$this->request->post('express_no');
        $express_code=$this->request->post('express_code');
        
        $order->updateStatus([
            'status'=>2,
            'express_no'=>$express_no,
            'express_code'=>$express_code
        ]);
        user_log($this->mid,'orderdelivery',1,'订单发货 '.$id ,'manager');
        $this->success('操作成功');
    }
    public function setreceive($id){
        $order = OrderModel::get($id);
        if(empty($id) || empty($order)){
            $this->error('订单不存在');
        }
        if($order['status'] < 0){
            $this->error('订单已失效');
        }
        if($order['status'] < 2){
            $this->error('订单未发货');
        }
        if($order['status'] >= 3){
            $this->error('订单已完成');
        }
        $order->updateStatus(['status'=>3]);
        user_log($this->mid,'orderconfirm',1,'订单确认 '.$id ,'manager');
        $this->success('操作成功');
    }

    /**
     * 订单进度修改
     * @param $id
     */
    public function status($id){
        $this->error('操作已失效');
    }
    
    
    
    /**
     * 改价
     * @param $id
     * @param $price
     * @throws Exception
     */
    public function reprice($id,$price)
    {
        $order = OrderModel::get($id);
        if(empty($id) || empty($order)){
            $this->error('订单不存在');
        }
        if($order['status']!=0){
            $this->error('订单当前状态不可改价');
        }
        $price=$this->request->post('price');
        
        $data=array(
            'payamount'=>round(floatval($price),2)
        );
        
        $order->save($data);
        user_log($this->mid,'repriceorder',1,'订单改价 '.$id .' '.$price,'manager');
        $this->success('操作成功');
    }

    /**
     * 支付订单查询
     * @param $id
     */
    public function paystatus($id){
        $order = OrderModel::get($id);
        if(empty($id) || empty($order)){
            $this->error('订单不存在');
        }
        if($order['status']!=0){
            $this->error('订单当前状态非待支付');
        }
        $payorders = PayOrderModel::filterTypeAndId('order',$id)->select();
        if(empty($payorders)){
            $this->error('该订单没有在线支付记录');
        }
        
        $this->success('操作成功', null, ['lists'=>$payorders]);
    }

    /**
     * 支付状态查询 todo
     * @param $payid
     */
    public function payquery($payid){
        $payorder = PayOrderModel::get($payid);
        if(empty($payorder)){
            $this->error('支付订单不存在');
        }
        $result=$payorder->checkStatus();
        if($result){
            if($payorder->getErrNo()){
                $this->error('查询成功：'.$payorder->getError());
            }else{
                $this->success('订单已支付');
            }
        }else{
            $this->error($payorder->getError());
        }
    }

    /**
     * 审核订单
     * @param $id
     */
    public function audit($id){
        $order = OrderModel::get($id);
        if(empty($id) || empty($order)){
            $this->error('订单不存在');
        }
        $audit=$this->request->post('status/d');
        
        $order->audit();
        user_log($this->mid,'auditorder',1,'审核订单 '.$id .' '.$audit,'manager');
        $this->success('操作成功');
    }

    /**
     * 删除订单
     * @param $id
     */
    public function delete($id)
    {
        $model = Db::name('order');
        $result = $model->whereIn("order_id",idArr($id))->useSoftDelete('delete_time',time())->delete();
        if($result){
            //Db::name('orderProduct')->whereIn("order_id",idArr($id))->delete();
            user_log($this->mid,'deleteorder',1,'删除订单 '.$id ,'manager');
            $this->success(lang('Delete success!'), url('Order/index'));
        }else{
            $this->error(lang('Delete failed!'));
        }
    }
}