<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirn
 * Date: 2016/9/14
 * Time: 12:05
 */

namespace app\admin\controller;


class PaytypeController extends BaseController
{
    protected $paytypes;

    public function _initialize()
    {
        parent::_initialize();
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

        $this->pagelist($model,$where,'id DESC');
        $this->display();
    }

    /**
     * 添加付款方式
     */
    public function add()
    {
        //默认显示添加表单
        if (!$this->request->isPost()) {
            $this->assign('banklist',banklist());
            $this->display();
        }
        if ($this->request->isPost()) {
            //如果用户提交数据
            $model = D("Paytype");
            if (!$model->create()) {
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                $this->error($model->getError());
                exit();
            } else {
                $file=$this->upload('paytype','qrcodefile');
                if($file){
                    $model->qrcode=$file['url'];
                }
                if ($model->add()) {
                    $this->success("添加成功", url('Paytype/index'));
                } else {
                    $this->error("添加失败");
                }
            }
        }
    }
    /**
     * 更新付款方式
     */
    public function update($id)
    {
        $id = intval($id);
        //默认显示添加表单
        if (!$this->request->isPost()) {
            $model = Db::name('Paytype')->where("id= %d",$id)->find();
            $this->assign('model',$model);
            $this->assign('banklist',banklist());
            $this->display();
        }
        if ($this->request->isPost()) {
            $model = D("Paytype");
            if (!$model->create()) {
                $this->error($model->getError());
            }else{
                $file=$this->upload('paytype','qrcodefile');
                if($file){
                    $qrcode=$model->qrcode;
                    if(!empty($qrcode)){
                        unlink('.'.$qrcode);
                    }
                    $model->qrcode=$file['url'];

                }
                if ($model->save()) {
                    $this->success("更新成功", url('Paytype/index'));
                } else {
                    $this->error("更新失败");
                }
            }
        }
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
        $this->display();
    }
}