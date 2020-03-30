<?php

namespace app\index\controller\member;


use app\common\validate\MemberInvoiceValidate;
use think\Db;

/**
 * 发票资料控制器
 * Class InvoiceController
 * @package app\index\controller\member
 */
class InvoiceController extends BaseController
{
    public function index(){
        if($this->request->isPost()){
            $data=$this->request->only('id','post');
            $result=Db::name('MemberInvoice')->where('member_id',$this->userid)
                ->whereIn('id',idArr($data['id']))->delete();
            if($result){
                user_log($this->userid,'invoicedel',1,'删除发票资料:'.$data['id']);
                $this->success('删除成功！');
            }else{
                $this->error('删除失败！');
            }
        }
        $invoices=Db::name('MemberInvoice')->where('member_id',$this->userid)->select();
        $this->assign('invoices',$invoices);
        return $this->fetch();
    }

    public function add(){
        if($this->request->isPost()){
            $data=$this->request->only('title,type,tax_no,address,telephone,bank,caedno,is_default','post');
            $data['is_default']=empty($data['is_default'])?0:1;
            $validate=new MemberInvoiceValidate();
            if(!$validate->check($data)){
                $this->error($validate->getError());
            }else {
                $data['member_id'] = $this->userid;
                $id = Db::name('MemberInvoice')->insert($data, false, true);
                if ($id) {
                    user_log($this->userid, 'invoiceadd', 1, '添加发票资料:' . $id);
                    $this->success('添加成功', aurl('index/member.invoice/index'), Db::name('MemberInvoice')->find($id));
                } else {
                    $this->error('添加失败');
                }
            }
        }
        $invoice=[];
        $count=Db::name('MemberInvoice')->where('member_id',$this->userid)->count();
        if($count<1){
            $invoice['is_default']=1;
        }
        $this->assign('invoice',$invoice);
        return $this->fetch('edit');
    }
    public function edit($id){
        $invoice = Db::name('MemberInvoice')
            ->where('member_id',$this->userid)
            ->where('id',$id)->find();
        if(empty($invoice)){
            $this->error('发票资料不存在');
        }
        if($this->request->isPost()){
            $data=$this->request->only('title,type,tax_no,address,telephone,bank,caedno,is_default','post');
            $data['is_default']=empty($data['is_default'])?0:1;
            $validate=new MemberInvoiceValidate();
            if(!$validate->check($data)){
                $this->error($validate->getError());
            }else{
                $result=Db::name('MemberInvoice')->where('member_id',$this->userid)
                    ->where('id',$id)->update($data);
                if($result){
                    user_log($this->userid,'invoiceedit',1,'修改发票资料:'.$id);
                    $this->success('修改成功',aurl('index/member.invoice/index'));
                }else{
                    $this->error('修改失败');
                }
            }

        }

        $this->assign('invoice',$invoice);
        return $this->fetch();
    }

    public function invoices(){
        return $this->fetch();
    }

    public function apply(){
        return $this->fetch();
    }
}