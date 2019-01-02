<?php

namespace app\admin\controller;


use app\admin\validate\PaytypeValidate;
use think\Db;

/**
 * 充值方式管理
 * Class PaytypeController
 * @package app\admin\controller
 */
class PaytypeController extends BaseController
{
    protected $paytypes;

    public function initialize()
    {
        parent::initialize();
        $this->paytypes=payTypes();
        $this->assign('paytypes',$this->paytypes);
    }

    /**
     * 列表
     * @param string $type
     * @return mixed
     */
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

    /**
     * 添加付款方式
     */
    public function add(){
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $validate = new PaytypeValidate();

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {

                $file = $this->upload('paytype', 'upload_qrcode');
                if ($file) {
                    $data['qrcode'] = $file['url'];

                }elseif($this->uploadErrorCode>102){
                    $this->error($this->uploadErrorCode.':'.$this->uploadError);
                }

                if (Db::name('Paytype')->insert($data)) {

                    $this->success(lang('Add success!'), url('Paytype/index'));
                } else {
                    delete_image($data['qrcode']);
                    $this->error(lang('Add failed!'));
                }
            }
        }
        $model=array('status'=>1,'type'=>'unioncard');
        $this->assign('model',$model);
        $this->assign('id',0);
        $this->assign('banklist',banklist());
        return $this->fetch('edit');
    }

    /**
     * 修改
     * @param $id
     * @return mixed
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
                $delete_images=[];
                $file=$this->upload('paytype','qrcodefile');
                if($file){
                    $data['qrcode']=$file['url'];
                    if(!empty($data['delete_qrcode']))$delete_images[]=$data['delete_qrcode'];
                }elseif($this->uploadErrorCode>102){
                    $this->error($this->uploadErrorCode.':'.$this->uploadError);
                }
                unset($data['delete_qrcode']);

                $data['id']=$id;
                if (Db::name('Paytype')->update($data)) {
                    delete_image($delete_images);
                    $this->success(lang('Update success!'), url('Paytype/index'));
                } else {
                    delete_image($data['qrcode']);
                    $this->error(lang('Update failed!'));
                }

            }
        }


        $model = Db::name('Paytype')->where('id', $id)->find();
        if(empty($model)){
            $this->error('支付方式不存在');
        }
        $this->assign('model',$model);
        $this->assign('id',$id);
        $this->assign('banklist',banklist());
        return $this->fetch();
    }

    /**
     * 删除
     * @param $id
     */
    public function delete($id)
    {
        $id = intval($id);
        $model = Db::name('Paytype');
        $result = $model->delete($id);
        if($result){
            $this->success(lang('Delete success!'), url('Paytype/index'));
        }else{
            $this->error(lang('Delete failed!'));
        }
    }

    public function recharge()
    {
        return $this->fetch();
    }
}