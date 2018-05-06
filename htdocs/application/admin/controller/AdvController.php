<?php
/**
 * 广告功能
 * User: shirne
 * Date: 2018/4/17
 * Time: 8:36
 */

namespace app\admin\controller;


use app\admin\validate\AdvGroupValidate;
use app\admin\validate\AdvItemValidate;
use think\Db;

class AdvController extends BaseController
{
    public function index(){
        $model = Db::name('AdvGroup');
        $where=array();
        if(!empty($key)){
            $where[] = array('title|flag','like',"%$key%");
        }
        $lists=$model->where($where)->order('id DESC')->paginate(15);
        $this->assign('lists',$lists);
        $this->assign('page',$lists->render());
        return $this->fetch();
    }

    public function add(){
        if ($this->request->isPost()) {
            $data=$this->request->post();
            $validate=new AdvGroupValidate();
            $validate->setId();
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }else{
                if (Db::name("AdvGroup")->insert($data)) {
                    $this->success("添加成功", url('adv/index'));
                } else {
                    $this->error("添加失败");
                }
            }
        }
        $model=array('status'=>1);
        $this->assign('model',$model);
        $this->assign('id',0);
        return $this->fetch('update');
    }

    /**
     * 添加/修改
     */
    public function update($id)
    {
        $id = intval($id);

        if ($this->request->isPost()) {
            $data=$this->request->post();
            $validate=new AdvGroupValidate();
            $validate->setId($id);

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }else{
                $model = Db::name("AdvGroup");

                $data['id']=$id;
                if ($model->update($data)) {
                    $this->success("更新成功", url('adv/index'));
                } else {
                    $this->error("更新失败");
                }
            }
        }else{

            $model = Db::name('AdvGroup')->where(["id"=> $id])->find();
            if(empty($model)){
                $this->error('广告组不存在');
            }
            $this->assign('model',$model);
            $this->assign('id',$id);
            return $this->fetch();
        }
    }
    /**
     * 删除广告位
     */
    public function delete($id)
    {
        $id = intval($id);
        $force=$this->request->post('force/d',0);
        $model = Db::name('AdvGroup');
        $count=Db::name('AdvItem')->where(array('group_id'=>$id))->count();
        if($count<1 || $force!=0) {
            $result = $model->delete($id);
        }else{
            $this->error("广告位中还有广告项目");
        }
        if($result){
            if($count>0){
                Db::name('AdvItem')->where(array('group_id'=>$id))->delete();
            }
            $this->success("广告位删除成功", url('adv/index'));
        }else{
            $this->error("广告位删除失败");
        }
    }

    public function itemlist($gid){
        $model = Db::name('AdvItem');
        $group=Db::name('AdvGroup')->find($gid);
        if(empty($group)){
            $this->error('广告位不存在');
        }
        $where=array('group_id'=>$gid);
        if(!empty($key)){
            $where[] = array('title|url','like',"%$key%");
        }
        $lists=$model->where($where)->order('sort ASC,id DESC')->paginate(15);
        $this->assign('lists',$lists);
        $this->assign('page',$lists->render());
        $this->assign('gid',$gid);
        return $this->fetch();
    }

    public function itemadd($gid){
        if ($this->request->isPost()) {
            $data=$this->request->post();
            $validate=new AdvItemValidate();

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }else{
                $uploaded=$this->upload('adv','upload_image',true);
                if(!empty($uploaded)){
                    $data['image']=$uploaded['url'];
                }
                $model = Db::name("AdvItem");
                $url=url('adv/itemlist',array('gid'=>$gid));
                if ($model->insert($data)) {
                    $this->success("添加成功",$url);
                } else {
                    $this->error("添加失败");
                }
            }
        }
        $model=array('status'=>1,'group_id'=>$gid);
        $this->assign('model',$model);
        $this->assign('id',0);
        return $this->fetch('itemupdate');
    }

    /**
     * 添加/修改
     */
    public function itemupdate($id)
    {
        $id = intval($id);

        if ($this->request->isPost()) {
            $data=$this->request->post();
            $validate=new AdvItemValidate();

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }else{
                $model = Db::name("AdvItem");
                $url=url('adv/itemlist',array('gid'=>$data['group_id']));
                $delete_images=[];
                $uploaded=$this->upload('adv','upload_image',true);
                if(!empty($uploaded)){
                    $data['image']=$uploaded['url'];
                    $delete_images[]=$data['delete_image'];
                }
                unset($data['delete_image']);
                $data['id']=$id;
                if ($model->update($data)) {
                    delete_image($delete_images);
                    $this->success("更新成功", $url);
                } else {
                    $this->error("更新失败");
                }
            }
        }else{
            $model = Db::name('AdvItem')->where(["id"=> $id])->find();
            if(empty($model)){
                $this->error('广告项不存在');
            }

            $this->assign('model',$model);
            $this->assign('id',$id);
            return $this->fetch();
        }
    }
    /**
     * 删除广告位
     */
    public function itemdelete($gid,$id)
    {
        $id = intval($id);
        $model = Db::name('AdvItem');
        $result = $model->delete($id);
        if($result){
            $this->success("广告删除成功", url('adv/itemlist',array('gid'=>$gid)));
        }else{
            $this->error("广告删除失败");
        }
    }
}