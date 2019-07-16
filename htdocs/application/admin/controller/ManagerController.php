<?php
namespace app\admin\controller;

use app\admin\model\ManagerModel;
use app\admin\validate\ManagerValidate;
use app\common\command\Manager;
use think\Db;


/**
 * 管理员管理
 * Class ManagerController
 * @package app\admin\controller
 */
class ManagerController extends BaseController
{

    /**
     * 用户列表
     * @param string $key
     * @return mixed|\think\response\Redirect
     */
    public function index($key="")
    {
        if($this->request->isPost()){
            return redirect(url('',['key'=>base64_encode($key)]));
        }
        $key=empty($key)?"":base64_decode($key);
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

    /**
     * 管理员日志
     * @param string $key
     * @param string $type
     * @param int $member_id
     * @return mixed
     */
    public function log($key='',$type='',$manager_id=0){
        if($this->request->isPost()){
            return redirect(url('',['key'=>base64_encode($key)]));
        }

        $model=Db::view('ManagerLog','*')
            ->view('Manager',['username'],'ManagerLog.manager_id=Manager.id','LEFT');

        if(!empty($key)){
            $key = base64_decode($key);
            $model->whereLike('ManagerLog.remark',"%$key%");
        }
        if(!empty($type)){
            $model->where('action',$type);
        }
        if($manager_id!=0){
            $model->where('manager_id',$manager_id);
        }

        $logs = $model->order('ManagerLog.id DESC')->paginate(15);
        $this->assign('logs', $logs);
        $this->assign('keyword', $key);
        $this->assign('page',$logs->render());
        return $this->fetch();
    }

    /**
     * 日志详情
     * @param $id
     * @return mixed
     */
    public function logview($id){

        $model=Db::name('ManagerLog')->find($id);
        $manager=Db::name('Manager')->find($model['manager_id']);

        $this->assign('model', $model);
        $this->assign('manager', $manager);
        return $this->fetch();
    }

    /**
     * 清除日志
     */
    public function logclear(){
        $date=$this->request->get('date');
        $d=strtotime($date);
        if(empty($d)){
            $d=strtotime('-7days');
        }

        Db::name('ManagerLog')->where('create_time','ELT',$d)->delete();
        user_log($this->mid,'clearlog',1,'清除日志' ,'manager');
        $this->success("清除完成");
    }

    /**
     * 添加
     * @return mixed
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
                if($this->manage['type'] > $data['type']){
                    $this->error('您没有权限添加该类型账号');
                }
                $data['pid']=$this->mid;
                unset($data['repassword']);
                $model=ManagerModel::create($data);
                if ($model->id) {
                    user_log($this->mid,'addmanager',1,'添加管理员'.$model->id ,'manager');
                    $this->success(lang('Add success!'), url('manager/index'));
                } else {
                    $this->error(lang('Add failed!'));
                }
            }
        }
        $model=array('type'=>2,'status'=>1);
        $this->assign('model',$model);
        return $this->fetch('update');
    }

    /**
     * 修改
     * @param $id
     * @return mixed
     */
    public function update($id)
    {
        $id=intval($id);
        if($id==0)$this->error('参数错误');
        $model=ManagerModel::get($id);
        if($this->manage['type']>$model['type']){
            $this->error('您没有权限查看该管理员');
        }
        
        if ($this->request->isPost()) {
            if(!$model->hasPermission($this->mid)){
                $this->error('您没有权限编辑该管理员资料');
            }
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
                if($this->manage['type']>$data['type']){
                    $this->error('您不能将该管理员设置为更高级的管理员');
                }
                
                //强制更改超级管理员用户类型
                if(config('SUPER_ADMIN_ID') ==$id){
                    $data['type'] = 1;
                }else{
                    $parent = Db::name('manage')->where('id',$model['pid'])->find();
                    if(!empty($parent)){
                        if($data['type']<$parent['type']){
                            $this->error('不能将管理员类型设置为比上级高的类型');
                        }
                    }
                }
                
                //更新
                if ($model->allowField(true)->update($data)) {
                    user_log($this->mid,'addmanager',1,'修改管理员'.$model->id ,'manager');
                    $this->success(lang('Update success!'), url('manager/index'));
                } else {
                    $this->error(lang('Update failed!'));
                }        
            }
        }
        
        $this->assign('model',$model);
        return $this->fetch();
    }

    /**
     * 管理员权限
     * @param $id
     * @return mixed
     */
    public function permision($id){
        $id=intval($id);
        if($id==0)$this->error('参数错误');
        $manager=ManagerModel::get($id);
        if(empty($manager)){
            $this->error('管理员资料错误');
        }
        if(!$manager->hasPermission($this->mid)){
            $this->error('您不能编辑该管理员的权限');
        }
        $model = Db::name('ManagerPermision')->where('manager_id',$id)->find();
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
                $this->success(lang('Update success!'), url('manager/index'));
            }else {
                $this->error(lang('Update failed!'));
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
     * @param $id
     */
    public function delete($id)
    {
    	$id = intval($id);
    	if(1 == $id) $this->error("超级管理员不可禁用!");

        //查询status字段值
        $result = ManagerModel::where('id',$id)->find();
        $data=array();
        if($result['status'] == 1){
        	$data['status']=0;
        }
        if($result['status'] == 0){
        	$data['status']=1;
        }
        if($result->save($data)){
            $this->success(lang('Update success!'), url('manager/index'));
        }else{
            $this->error(lang('Update failed!'));
        }
    }
}
