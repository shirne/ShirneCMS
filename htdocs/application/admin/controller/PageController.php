<?php
namespace app\admin\controller;
use app\admin\model\PageModel;
use app\admin\validate\PageValidate;
use think\Db;

/**
 * 单页管理
 */
class PageController extends BaseController
{
    /**
     * 单页列表
     */
    public function index($key="")
    {
        $model = Db::name('page');
        $where=array();
        if(!empty($key)){
            $where['title'] = array('title|name','like',"%$key%");
        }
        $lists=$model->where($where)->order('ID DESC')->paginate(15);
        $this->assign('lists',$lists);
        $this->assign('page',$lists->render());
        return $this->fetch();
    }

    public function add(){
        if ($this->request->isPost()) {
            $data=$this->request->post();
            $validate=new PageValidate();
            $validate->setId();
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                $model=PageModel::create($data);
                if ($model->getLastInsID()) {
                    $this->success("添加成功", url('page/index'));
                } else {
                    $this->error("添加失败");
                }
            }
        }
        $model=array();
        $this->assign('page', $model);
        $this->assign('id', 0);
        return $this->fetch('edit');
    }

    /**
     * 添加单页
     */
    public function edit($id)
    {
        if ($this->request->isPost()) {
            $data=$this->request->post();
            $validate=new PageValidate();
            $validate->setId($id);
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                $model=PageModel::get($id);

                if ($model->allowField(true)->save($data)) {
                    $this->success("更新成功", url('page/index'));
                } else {
                    $this->error("更新失败");
                }
            }
        }
        $model = Db::name('page')->where(["id"=> $id])->find();
        if(empty($model)){
            $this->error('要编辑的内容不存在');
        }
        $this->assign('page', $model);
        $this->assign('id', $id);
        return $this->fetch();
    }
    /**
     * 删除单页
     */
    public function delete($id)
    {
    		$id = intval($id);
        $model = Db::name('page');
        $result = $model->where(["id"=>$id])->delete();
        if($result){
            $this->success("删除成功", url('page/index'));
        }else{
            $this->error("删除失败");
        }
    }
}
