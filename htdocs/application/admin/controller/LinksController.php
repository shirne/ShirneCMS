<?php
namespace app\admin\controller;
use app\index\validate\LinksValidate;
use think\Db;

/**
 * 链接管理
 */
class LinksController extends BaseController
{
    /**
     * 链接列表
     */
    public function index($key="")
    {
        $model = Db::name('links');
        $where=array();
        if(!empty($key)){
            $where[] = array('title|url','like',"%$key%");
        }
        $lists=$model->where($where)->order('ID DESC')->paginate(15);
        $this->assign('lists',$lists);
        $this->assign('page',$lists->render());
        $this->display();
    }

    /**
     * 编辑链接
     */
    public function edit($id=0)
    {
        if ($this->request->isPost()) {
            //如果用户提交数据
            $data = $this->request->post();
            $validate=new LinksValidate();

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                if($id>0){
                    $data['id']=$id;
                    if (Db::name('Links')->update($data)) {
                        $this->success("更新成功", url('links/index'));
                    } else {
                        $this->error("更新失败");
                    }
                }else {
                    if (Db::name('Links')->insert($data)) {
                        $this->success("链接添加成功", url('links/index'));
                    } else {
                        $this->error("链接添加失败");
                    }
                }
            }
        }

        if($id>0) {
            $model = Db::name('Links')->find($id);
        }else{
            $model=array();
        }
        $this->assign('model',$model);
        $this->display();
    }
    /**
     * 删除链接
     */
    public function delete($id)
    {
        $id = intval($id);
        $model = Db::name('links');
        $result = $model->delete($id);
        if($result){
            $this->success("链接删除成功", url('links/index'));
        }else{
            $this->error("链接删除失败");
        }
    }
}
