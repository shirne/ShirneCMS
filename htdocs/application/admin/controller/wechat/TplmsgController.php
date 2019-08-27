<?php


namespace app\admin\controller\wechat;


use app\common\model\WechatTemplateMessageModel;

class TplmsgController extends WechatBaseController
{
    public function index(){
        
        $msgs = [
            'order_need_pay'=>['title'=>'待付款提醒','keywords'=>'订单号、待付金额、商品详情、支付提醒'],
            'order_payed'=>['title'=>'订单支付成功通知','keywords'=>'订单号码、订单金额、下单时间、物品名称'],
            'order_deliver'=>['title'=>'订单发货提醒','keywords'=>'快递公司、发货时间、购买时间、物品名称'],
            'order_complete'=>['title'=>'订单完成通知','keywords'=>'订单号码、订单金额、商品名称、确认时间'],
            'order_cancel'=>['title'=>'订单取消通知','keywords'=>'订单编号、订单金额、物品详情、取消原因']
        ];
        
        $tpls = WechatTemplateMessageModel::getTpls($this->wid);
        
        if($this->request->isPost()){
            $datas = $this->request->post('tpls');
            foreach ($msgs as $key=>$msg){
                if(isset($tpls[$key])){
                    WechatTemplateMessageModel::update($datas[$key],['wechat_id'=>$this->wid,'type'=>$key]);
                }elseif(!empty($datas[$key]['template_id'])){
                    $datas[$key]['wechat_id']=$this->wid;
                    $datas[$key]['type']=$key;
                    WechatTemplateMessageModel::create($datas[$key]);
                }
            }
            $this->success('保存成功');
        }
        $this->assign('msgs',$msgs);
        $this->assign('tpls',$tpls);
        return $this->fetch();
    }
}