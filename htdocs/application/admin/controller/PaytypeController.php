<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirn
 * Date: 2016/9/14
 * Time: 12:05
 */

namespace app\admin\controller;


use app\admin\validate\PaytypeValidate;
use think\Db;

class PaytypeController extends BaseController
{
    protected $paytypes;

    public function initialize()
    {
        parent::initialize();
        $this->paytypes=payTypes();
        $this->assign('paytypes',$this->paytypes);
    }

    public function index($type='')
    {
        $model = Db::name('Paytype');
        $where=array();
        if(!empty($type )){
            $where['type'] = $type;
        }

        $lists=$model->where($where)->order('ID DESC')->paginate(15);
        $this->assign('lists',$lists);
        $this->assign('page',$lists->render());
        return $this->fetch();
    }

    public function add(){
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $validate = new PaytypeValidate();

            if (!$validate->check($data)) {
                $this->error($validate->getError());
                exit();
            } else {
                $file = $this->upload('paytype', 'qrcodefile');
                if ($file) {
                    $data['qrcode'] = $file['url'];
                }
                if (Db::name('Paytype')->insert($data)) {
                    $this->success("添加成功", url('Paytype/index'));
                } else {
                    $this->error("添加失败");
                }
            }
        }
        $model=array();
        $this->assign('model',$model);
        $this->assign('id',0);
        $this->assign('banklist',banklist());
        return $this->fetch('edit');
    }

    /**
     * 添加付款方式
     */
    public function edit($id)
    {
        if ($this->request->isPost()) {
            $data=$this->request->post();
            $validate=new PaytypeValidate();

            if (!$validate->check($data)) {
                $this->error($validate->getError());
                exit();
            } else {
                $file=$this->upload('paytype','qrcodefile');
                if($file){
                    $data['qrcode']=$file['url'];
                }
                $data['id']=$id;
                if (Db::name('Paytype')->update($data)) {
                    $this->success("更新成功", url('Paytype/index'));
                } else {
                    $this->error("更新失败");
                }

            }
        }


        $model = Db::name('Paytype')->where(["id"=> $id])->find();
        if(empty($model)){
            $this->error('支付方式不存在');
        }
        $this->assign('model',$model);
        $this->assign('id',$id);
        $this->assign('banklist',banklist());
        return $this->fetch();
    }

    /**
     * 删除付款方式
     */
    public function delete($id)
    {
        $id = intval($id);
        $model = Db::name('Paytype');
        $result = $model->delete($id);
        if($result){
            $this->success("付款方式删除成功", url('Paytype/index'));
        }else{
            $this->error("付款方式删除失败");
        }
    }

    public function recharge()
    {
        return $this->fetch();
    }
}