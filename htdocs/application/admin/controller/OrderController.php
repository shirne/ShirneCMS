<?php

namespace app\admin\controller;

use app\common\model\OrderModel;
use excel\Excel;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use think\Db;

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
            ->view('member',['username','realname','avatar','level_id'],'member.id=order.member_id','LEFT')
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
                $row['order_id'],order_status($row['status'],false),date('Y/m/d H:i:s',$row['create_at']),$row['member_id'],$row['username'],
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
        $this->assign('model',$model);
        $this->assign('member',$member);
        $this->assign('products',$products);
        return $this->fetch();
    }

    /**
     * 订单进度修改
     * @param $id
     */
    public function status($id){
        $order = OrderModel::get($id);
        if(empty($id) || empty($order)){
            $this->error('订单不存在');
        }
        $audit=$this->request->post('status/d');
        $express_no=$this->request->post('express_no');
        $express_code=$this->request->post('express_code');
        $data=array(
            'status'=>$audit
        );
        if(!empty($express_code)){
            $data['express_no']=$express_no;
            $data['express_code']=$express_code;
        }
        $order->save($data);
        user_log($this->mid,'auditorder',1,'更新订单 '.$id .' '.$audit,'manager');
        $this->success('操作成功');
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
        $order->save(['isaudit'=>$audit]);
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