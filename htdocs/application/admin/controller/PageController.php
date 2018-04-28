<?php
namespace app\admin\controller;
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
        $model = M('page');
        $where=array();
        if(!empty($key)){
            $where['title'] = array('like',"%$key%");
            $where['name'] = array('like',"%$key%");
            $where['_logic'] = 'or';
        }
        $this->pagelist($model,$where,'id DESC');
        $this->display();     
    }

    /**
     * 添加单页
     */
    public function add()
    {
        //默认显示添加表单
        if (!IS_POST) {
            $this->display();
        }
        if (IS_POST) {
            //如果用户提交数据
            $model = D("Page");
            if (!$model->create()) {
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                $this->error($model->getError());
                exit();
            } else {
                if ($model->add()) {
                    $this->success("添加成功", U('page/index'));
                } else {
                    $this->error("添加失败");
                }
            }
        }
    }
    /**
     * 更新单页信息
     */
    public function update($id)
    {
    		$id = intval($id);
        //默认显示添加表单
        if (!IS_POST) {
            $model = M('page')->where("id=%d",$id)->find();
            $this->assign('page',$model);
            $this->display();
        }
        if (IS_POST) {
            $model = D("Page");
            if (!$model->create()) {
                $this->error($model->getError());
            }else{
                if ($model->save()) {
                    $this->success("更新成功", U('page/index'));
                } else {
                    $this->error("更新失败");
                }        
            }
        }
    }
    /**
     * 删除单页
     */
    public function delete($id)
    {
    		$id = intval($id);
        $model = M('page');
        $result = $model->where("id=%d",$id)->delete();
        if($result){
            $this->success("删除成功", U('page/index'));
        }else{
            $this->error("删除失败");
        }
    }
}
