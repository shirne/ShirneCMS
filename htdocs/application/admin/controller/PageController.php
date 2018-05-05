<?php
namespace app\admin\controller;
use app\admin\model\PageModel;
use app\admin\validate\PageGroupValidate;
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
        $model = Db::view('page','*');
        $where=array();
        if(!empty($key)){
            $where[] = array('page.title|page.name|page.group','like',"%$key%");
        }
        $lists=$model->view('pageGroup',['group_name'],'pageGroup.group=page.group','LEFT')
            ->where($where)->order('sort ASC,ID DESC')->paginate(15);
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
        $this->assign('groups', getPageGroups());
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
        $this->assign('groups', getPageGroups());
        $this->assign('id', $id);
        return $this->fetch();
    }

    public function status($id,$type=0){
        $model = Db::name('page');
        $result = $model->where('id','in',idArr($id))->update(['status'=>intval($type)]);
        if($result){
            $this->success("设置成功", url('page/index'));
        }else{
            $this->error("设置失败");
        }
    }
    /**
     * 删除单页
     */
    public function delete($id)
    {
        $model = Db::name('page');
        $result = $model->where('id','in',idArr($id))->delete();
        if($result){
            $this->success("删除成功", url('page/index'));
        }else{
            $this->error("删除失败");
        }
    }

    public function groups(){
        $groups=getPageGroups(true);

        $this->assign('lists', $groups);
        return $this->fetch();
    }
    public function groupedit($id=0){
        if($this->request->isPost()){
            $data=$this->request->post();
            $validate=new PageGroupValidate();
            $validate->setId($id);
            if(!$validate->check($data)){
                $this->error($validate->getError());
            }
            if($id>0){
                Db::name('PageGroup')->where('id',$id)->update($data);
                cache('page_group',null);
                $this->success('保存成功',url('page/groups'));
            }else{
                Db::name('PageGroup')->where('id',$id)->insert($data);
                cache('page_group',null);
                $this->success('添加成功',url('page/groups'));
            }
        }
        if($id>0){
            $model=Db::name('PageGroup')->find($id);
        }else{
            $model=array('sort'=>99);
        }
        $this->assign('model', $model);
        $this->assign('id', $id);
        return $this->fetch();
    }
    public function groupdelete($id)
    {
        $id = idArr($id);
        $groups=Db::name('PageGroup')->where("id",'in',$id)->select();
        if(!empty($groups)) {
            $groups=array_column($groups,'group');
            $exists = Db::name('page')->where('group', 'in', $groups)->count();
            if ($exists > 0) {
                $this->error("选中的页面组还有内容");
            }
            $result = Db::name('PageGroup')->where("id", 'in', $id)->delete();
        }
        if($result){
            cache('page_group',NULL);
            $this->success("删除成功", url('page/groups'));
        }else{
            $this->error("删除失败");
        }
    }
}
