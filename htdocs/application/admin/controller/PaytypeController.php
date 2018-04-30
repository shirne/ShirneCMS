<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirn
 * Date: 2016/9/14
 * Time: 12:05
 */

namespace app\admin\controller;


use app\index\validate\PaytypeValidate;
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

    /**
     * 添加付款方式
     */
    public function edit($id=0)
    {
        if ($this->request->isPost()) {
            $data=$this->request->post();
            $validate=new PaytypeValidate();

            if (!$validate->check($data)) {
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                $this->error($validate->getError());
                exit();
            } else {
                $file=$this->upload('paytype','qrcodefile');
                if($file){
                    $data['qrcode']=$file['url'];
                }
                if($id>0){
                    $data['id']=$id;
                    if (Db::name('Paytype')->update($data)) {
                        $this->success("更新成功", url('Paytype/index'));
                    } else {
                        $this->error("更新失败");
                    }
                }else {
                    if (Db::name('Paytype')->insert($data)) {
                        $this->success("添加成功", url('Paytype/index'));
                    } else {
                        $this->error("添加失败");
                    }
                }
            }
        }

        if($id>0) {
            $model = Db::name('Paytype')->where(["id"=> $id])->find();
        }else{
            $model=array();
        }
        $this->assign('model',$model);
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