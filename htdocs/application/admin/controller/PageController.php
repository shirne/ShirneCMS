<?php
namespace app\admin\controller;
use app\admin\model\PageModel;
use app\index\validate\PageValidate;
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

    /**
     * 添加单页
     */
    public function edit($id=0)
    {
        if ($this->request->isPost()) {
            $data=$this->request->post();
            $validate=new PageValidate();
            $validate->setId($id);
            if (!$validate->check($data)) {
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                $this->error($validate->getError());
            } else {
                if($id>0){
                    $data['id']=$id;
                    if (PageModel::update($data)) {
                        $this->success("更新成功", url('page/index'));
                    } else {
                        $this->error("更新失败");
                    }
                }else {
                    if (PageModel::create($data)) {
                        $this->success("添加成功", url('page/index'));
                    } else {
                        $this->error("添加失败");
                    }
                }
            }
        }
        if($id>0) {
            $model = Db::name('page')->where(["id"=> $id])->find();
        }else{
            $model=array();
        }
        $this->assign('page', $model);
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
