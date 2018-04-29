<?php
namespace app\admin\controller;
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
            $where['title'] = array('like',"%$key%");
            $where['url'] = array('like',"%$key%");
            $where['_logic'] = 'or';
        }
        $this->pagelist($model,$where,'id DESC');

        $this->display();     
    }

    /**
     * 添加链接
     */
    public function add()
    {
        //默认显示添加表单
        if (!$this->request->isPost()) {
            $this->display();
        }
        if ($this->request->isPost()) {
            //如果用户提交数据
            $model = D("links");
            if (!$model->create()) {
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                $this->error($model->getError());
                exit();
            } else {
                if ($model->add()) {
                    $this->success("链接添加成功", url('links/index'));
                } else {
                    $this->error("链接添加失败");
                }
            }
        }
    }
    /**
     * 更新链接信息
     */
    public function update($id)
    {
        $id = intval($id);
        //默认显示添加表单
        if (!$this->request->isPost()) {
            $model = Db::name('links')->where("id= %d",$id)->find();
            $this->assign('model',$model);
            $this->display();
        }
        if ($this->request->isPost()) {
            $model = D("links");
            if (!$model->create()) {
                $this->error($model->getError());
            }else{
                if ($model->save()) {
                    $this->success("更新成功", url('links/index'));
                } else {
                    $this->error("更新失败");
                }        
            }
        }
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
