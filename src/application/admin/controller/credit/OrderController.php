<?php

namespace app\admin\controller\credit;

use app\admin\controller\BaseController;
use app\common\model\CreditOrderModel;
use app\common\model\PayOrderModel;
use shirne\excel\Excel;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use think\Db;

class OrderController extends BaseController
{
    public function index($key='',$status='',$audit=''){
        if($this->request->isPost()){
            return redirect(url('',['status'=>$status,'audit'=>$audit,'key'=>base64_encode($key)]));
        }
        $key=empty($key)?"":base64_decode($key);
        $model=Db::view('creditOrder','*')
            ->view('member',['username','realname','avatar','level_id'],'member.id=creditOrder.member_id','LEFT')
            ->where('creditOrder.delete_time',0);

        if(!empty($key)){
            $model->whereLike('creditOrder.order_no|member.username|member.nickname|member.realname|creditOrder.recive_name|creditOrder.mobile',"%$key%");
        }
        if($status!==''){
            $model->where('creditOrder.status',$status);
        }
        if($audit!==''){
            $model->where('creditOrder.isaudit',$audit);
        }

        $lists=$model->where('creditOrder.delete_time',0)->order(Db::raw('if(creditOrder.status>-1,creditOrder.status,3) ASC,creditOrder.create_time DESC'))->paginate(15);
        if(!$lists->isEmpty()) {
            $orderids = array_column($lists->items(), 'order_id');
            $prodata = Db::name('creditOrderGoods')->where('order_id', 'in', $orderids)->select();
            $goodss=array_index($prodata,'order_id',true);
            $lists->each(function($item) use ($goodss){
                if(isset($goodss[$item['order_id']])){
                    $item['goodss']=$goodss[$item['order_id']];
                }else {
                    $item['goodss'] = [];
                }
                return $item;
            });
        }

        $this->assign('keyword',$key);
        $this->assign('status',$status);
        $this->assign('audit',$audit);
        $this->assign('orderids',empty($orderids)?0:implode(',',$orderids));
        $this->assign('expresscodes',config('express.'));
        $this->assign('lists',$lists);
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
        $model=Db::view('creditOrder','*')
            ->view('member',['username','realname','avatar','level_id'],'member.id=creditOrder.member_id','LEFT')
            ->where('creditOrder.delete_time',0);
        if(empty($order_ids)){
            if(!empty($key)){
                $model->whereLike('creditOrder.order_no|member.username|member.realname|creditOrder.recive_name|creditOrder.mobile',"%$key%");
            }
            if($status!==''){
                $model->where('creditOrder.status',$status);
            }
            if($audit!==''){
                $model->where('creditOrder.isaudit',$audit);
            }
        }elseif($order_ids=='status') {
            $model->where('status',1);
        }else{
            $model->whereIn('order_id',idArr($order_ids));
        }


        $rows=$model->select();
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
            $prodata = Db::name('creditOrderGoods')->where('order_id', $row['order_id'])->find();
            $excel->addRow(array(
                $row['order_id'],order_status($row['status'],false),date('Y/m/d H:i:s',$row['create_time']),$row['member_id'],$row['username'],
                $prodata['goods_title'],$row['payamount'],$row['recive_name'],$row['mobile'],$row['province'],$row['city'],$row['area'],$row['address']
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
        $model=Db::name('creditOrder')->where('order_id',$id)->find();
        if(empty($model))$this->error('订单不存在');
        $member=Db::name('Member')->find($model['member_id']);
        $goodss = Db::name('creditOrderGoods')->where('order_id',  $id)->select();
        $this->assign('model',$model);
        $this->assign('member',$member);
        $this->assign('goodss',$goodss);
        return $this->fetch();
    }

    public function setcancel($id){
        $order = CreditOrderModel::get($id);
        if(empty($id) || empty($order)){
            $this->error('订单不存在');
        }
        if($order['status'] > 0){
            $this->error('订单不可取消');
        }
        $order->updateStatus(['status'=>-1]);
        user_log($this->mid,'cancelcreditorder',1,'取消积分订单 '.$id ,'manager');
        $this->success('操作成功');
    }
    public function setpayed($id){
        $order = CreditOrderModel::get($id);
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
            $debit = money_log($order['member_id'], -$order['payamount']*100, "积分订单支付", 'consume',0,'money');
            if(!$debit){
                $this->error('用户余额不足');
            }
        }else{
            $paytype = 'offline';
        }
        $order->updateStatus(['status'=>1,'pay_type'=>$paytype]);
        user_log($this->mid,'creditorderpay',1,'积分订单支付 '.$id ,'manager');
        $this->success('操作成功');
    }
    public function setdelivery($id){
        $order = CreditOrderModel::get($id);
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
        user_log($this->mid,'creditorderdelivery',1,'积分订单发货 '.$id ,'manager');
        $this->success('操作成功');
    }
    public function setreceive($id){
        $order = CreditOrderModel::get($id);
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
        user_log($this->mid,'creditorderconfirm',1,'积分订单确认 '.$id ,'manager');
        $this->success('操作成功');
    }

    public function setcomplete($id){
        $order = CreditOrderModel::get($id);
        if(empty($id) || empty($order)){
            $this->error('订单不存在');
        }
        if($order['status'] < 0){
            $this->error('订单已失效');
        }
        if($order['status'] < 3){
            $this->error('订单未收货');
        }
        if($order['status'] >= 4){
            $this->error('订单已完成');
        }
        $order->updateStatus(['status'=>4]);
        user_log($this->mid,'creditordercomplete',1,'积分订单完成 '.$id ,'manager');
        $this->success('操作成功');
    }

    /**
     * 订单进度修改
     * @param $id
     */
    public function status($id){
        $this->success('操作已失效');
    }
    /**
     * 改价
     * @param $id
     * @param $price
     * @throws Exception
     */
    public function reprice($id,$price)
    {
        $order = CreditOrderModel::get($id);
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
        user_log($this->mid,'repricecreditorder',1,'积分订单改价 '.$id .' '.$price,'manager');
        $this->success('操作成功');
    }

    /**
     * 支付订单查询
     * @param $id
     */
    public function paystatus($id){
        $order = CreditOrderModel::get($id);
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
     * 删除订单
     * @param $id
     */
    public function delete($id)
    {
        $model = Db::name('creditOrder');
        $result = $model->whereIn("order_id",idArr($id))->useSoftDelete('delete_time',time())->delete();
        if($result){
            user_log($this->mid,'deleteorder',1,'删除订单 '.$id ,'manager');
            $this->success("删除成功", url('credit.order/index'));
        }else{
            $this->error("删除失败");
        }
    }
}