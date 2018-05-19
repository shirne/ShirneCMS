<?php
namespace app\admin\controller;

use app\admin\model\ManagerModel;
use app\admin\validate\ManagerValidate;
use think\Db;


/**
 * 管理员管理
 */
class ManagerController extends BaseController
{

    /**
     * 用户列表
     */
    public function index($key="")
    {
        $model=Db::name('Manager');
        $where=array();
        if(!empty($key )){
            $where[] = array('username|email','like',"%$key%");
        }

        $lists=$model->where($where)->order('ID ASC')->paginate(15);
        $this->assign('lists',$lists);
        $this->assign('page',$lists->render());
        return $this->fetch();
    }

    public function log($key=''){
        $model=Db::view('ManagerLog','*')
            ->view('Manager',['username'],'ManagerLog.manager_id=Manager.id','LEFT');
        $where=array();
        if(!empty($key)){
            $where[]=['ManagerLog.remark','like',"%$key%"];
        }

        $logs = $model->where($where)->order('ManagerLog.id DESC')->paginate(15);
        $this->assign('logs', $logs);
        $this->assign('page',$logs->render());
        return $this->fetch();
    }
    public function logview($id){

        $model=Db::name('ManagerLog')->find($id);
        $manager=Db::name('Manager')->find($model['manager_id']);

        $this->assign('model', $model);
        $this->assign('manager', $manager);
        return $this->fetch();
    }

    public function logclear(){
        $date=$this->request->get('date');
        $d=strtotime($date);
        if(empty($d)){
            $date=date_sub(new \DateTime(date('Y-m-d')),new \DateInterval('P1M'));
            $d=$date->getTimestamp();
        }

        Db::name('ManagerLog')->where(array("create_time"=>array('ELT',$d)))->delete();
        user_log($this->mid,'clearlog',1,'清除日志' ,'manager');
        $this->success("清除完成");
    }

    /**
     * 添加用户
     */
    public function add()
    {
        if ($this->request->isPost()) {

            $data = $this->request->post();
            $validate=new ManagerValidate();
            $validate->setId();
            if (!$validate->check($data)) {
                $this->error($validate->getError());
                exit();
            } else {
                $data['salt']=random_str(8);
                $data['password']=encode_password($data['password'],$data['salt']);
                $data['last_view_member']=time();
                unset($data['repassword']);
                $model=ManagerModel::create($data);
                if ($model->id) {
                    user_log($this->mid,'addmanager',1,'添加管理员'.$model->id ,'manager');
                    $this->success("添加成功", url('manager/index'));
                } else {
                    $this->error("添加失败");
                }
            }
        }
        $model=array('type'=>2,'status'=>1);
        $this->assign('model',$model);
        return $this->fetch('update');
    }
    /**
     * 更新管理员信息
     */
    public function update($id)
    {
        $id=intval($id);
        if($id==0)$this->error('参数错误');

        if ($this->request->isPost()) {
            $data = $this->request->post();
            $validate=new ManagerValidate();
            $validate->setId($id);
            if (!$validate->scene('edit')->check($data)) {
                $this->error($validate->getError());
            }else{
                if(!empty($data['password'])){
                    $data['salt']=random_str(8);
                    $data['password'] = encode_password($data['password'],$data['salt']);
                }else{
                    unset($data['password']);
                }
                //强制更改超级管理员用户类型
                if(config('SUPER_ADMIN_ID') ==$id){
                    $data['type'] = 1;
                }
                $model=ManagerModel::get($id);
                //更新
                if ($model->allowField(true)->update($data)) {
                    user_log($this->mid,'addmanager',1,'修改管理员'.$model->id ,'manager');
                    $this->success("更新成功", url('manager/index'));
                } else {
                    $this->error("未做任何修改,更新失败");
                }        
            }
        }
        $model = Db::name('Manager')->find($id);
        $this->assign('model',$model);
        return $this->fetch();
    }

    /**
     * 管理员权限
     */
    public function permision($id){
        $id=intval($id);
        if($id==0)$this->error('参数错误');
        $model = Db::name('ManagerPermision')->where(array('manager_id'=>$id))->find();
        if(empty($model)){
            $model=array();
            $model['manager_id']=$id;
            $model['global']='';
            $model['detail']='';
            $model['id']=Db::name('manager_permision')->insert($model,false,true);
        }
        if($this->request->isPost()){
            $model['global']=$_POST['global'];
            if(!is_array($model['global']))$model['global']=array();
            $model['global']=implode(',',$model['global']);
            $model['detail']=$_POST['detail'];
            if(!is_array($model['detail']))$model['detail']=array();
            $model['detail']=implode(',',$model['detail']);
            if(Db::name('ManagerPermision')->update($model)){
                $this->success("更新成功", url('manager/index'));
            }else {
                $this->error("未做任何修改,更新失败");
            }
        }
        $model['global']=explode(',',$model['global']);
        $model['detail']=explode(',',$model['detail']);
        $this->assign('model',$model);
        $this->assign('perms',config('permisions.'));
        return $this->fetch();
    }
    /**
     * 删除管理员
     */
    public function delete($id)
    {
    	$id = intval($id);
    	if(config('SUPER_ADMIN_ID') == $id) $this->error("超级管理员不可禁用!");

        //查询status字段值
        $result = Db::name('Manager')->find($id);
        $data=array();
        if($result['status'] == 1){
        	$data['status']=0;
        }
        if($result['status'] == 0){
        	$data['status']=1;
        }
        if($result->save($data)){
            $this->success("状态更新成功", url('manager/index'));
        }else{
            $this->error("状态更新失败");
        }
    }
}
