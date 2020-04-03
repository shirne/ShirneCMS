<?php
namespace app\admin\controller;

use app\admin\model\ManagerModel;
use app\admin\model\ManagerRoleModel;
use app\admin\validate\ManagerValidate;
use app\common\command\Manager;
use think\facade\Db;


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
        if(!empty($key )){
            $model->whereLike('username|email',"%$key%");
        }
        
        $lists=$model->order('ID ASC')->paginate(15);
        $this->assign('lists',$lists);
        $this->assign('roles',ManagerRoleModel::getRoles());
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

        Db::name('ManagerLog')->where('create_time','<=',$d)->delete();
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
                if($this->manager['type'] > $data['type']){
                    $this->error('您没有权限添加该类型账号');
                }
                $data['pid']=$this->mid;
                unset($data['repassword']);
                $model=ManagerModel::create($data);
                if ($model->id) {
                    //添加默认权限
                    if($data['type']>1)$this->updatePermission($model->id,$data['type']);
                    
                    user_log($this->mid,'addmanager',1,'添加管理员'.$model->id ,'manager');
                    $this->success(lang('Add success!'), url('manager/index'));
                } else {
                    $this->error(lang('Add failed!'));
                }
            }
        }
        $model=array('type'=>ManagerRoleModel::max('type'),'status'=>1);
        $this->assign('model',$model);
        $this->assign('roles',ManagerRoleModel::getRoles());
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
        if($this->manager['type']>$model['type']){
            $this->error('您没有权限查看该管理员');
        }
        
        if ($this->request->isPost()) {
            if(!$model->hasPermission($this->mid)){
                $this->error('您没有权限编辑该管理员资料');
            }
            if(TEST_ACCOUNT == $model['username'] &&
                TEST_ACCOUNT == $this->manager['username']
            ){
                $this->error('演示账号，不可修改资料');
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
                if($this->manager['type']>$data['type']){
                    $this->error('您不能将该管理员设置为更高级的管理员');
                }
                
                //强制更改超级管理员用户类型
                if(SUPER_ADMIN_ID ==$id){
                    $data['type'] = 1;
                }else{
                    $parent = Db::name('manager')->where('id',$model['pid'])->find();
                    if(!empty($parent)){
                        if($data['type']<$parent['type']){
                            $this->error('不能将管理员类型设置为比上级高的类型');
                        }
                    }
                }
                $updatePermission=false;
                if($data['type']!=$model['type'])$updatePermission=true;
                //更新
                if ($model->allowField(true)->update($data)) {
                    if($updatePermission)$this->updatePermission($id,$data['type']);
                    user_log($this->mid,'addmanager',1,'修改管理员'.$model->id ,'manager');
                    $this->success(lang('Update success!'), url('manager/index'));
                } else {
                    $this->error(lang('Update failed!'));
                }        
            }
        }
        
        $this->assign('model',$model);
        $this->assign('roles',ManagerRoleModel::getRoles());
        return $this->fetch();
    }
    
    private function updatePermission($managerId,$global,$detail=null){
        if(is_null($detail)){
            $roles=ManagerRoleModel::getRoles();
            $role=$roles[$global];
            if(empty($role))return false;
            $global = $role['global'];
            $detail = $role['detail'];
        }
        $model['manager_id']=$managerId;
        $model['global']=is_array($global)?implode(',',$global):$global;
        $model['detail']=is_array($detail)?implode(',',$detail):$detail;
        Db::name('managerPermision')->insert($model,true);
        return true;
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
        $role = ManagerRoleModel::where('type',$manager['type'])->find();
        if(empty($role)){
            $this->error('请先设置管理员角色');
        }
        
        if(!$manager->hasPermission($this->mid)){
            $this->error('您不能编辑该管理员的权限');
        }
        $model = Db::name('ManagerPermision')->where('manager_id',$id)->find();
        if(empty($model)){
            $this->updatePermission($id,$role['global'],$role['detail']);
            $model = Db::name('ManagerPermision')->where('manager_id',$id)->find();
        }
        if($this->request->isPost()){
            
            list($global,$detail)=$role->filterPermissions($this->request->post('global'),$this->request->post('detail'));
            $model['global']=$global;
            $model['detail']=$detail;
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
        $this->assign('role',$role);
        return $this->fetch();
    }
    
    public function status($id, $status=0)
    {
        $id = intval($id);
        if(SUPER_ADMIN_ID == $id) {
            $this->error("创始人不可禁用!");
        }
        
        $manager = ManagerModel::get($id);
        if (!$manager->hasPermission($this->mid)) {
            $this->error('您不能设置该管理员的状态');
        }
        
        $data=[
            'status'=>$status==1?1:0
        ];
        if($manager->save($data)){
            $this->success(lang('Update success!'), url('manager/index'));
        }else{
            $this->error(lang('Update failed!'));
        }
    }

    /**
     * 删除管理员
     * @param $id
     */
    public function delete($id)
    {
    	$id = intval($id);
    	if(SUPER_ADMIN_ID == $id) {
            $this->error("创始人不可删除!");
        }
        
    	$manager = ManagerModel::get($id);
        if (!$manager->hasPermission($this->mid)) {
            $this->error('您不能删除该管理员');
        }
        $pid = $manager['pid'];
        $deleted=Db::name('manager')->where('id',$id)->delete();
        if($deleted){
            Db::name('managerPermision')->where('manager_id',$id)->delete();
            
            Db::name('manager')->where('pid',$id)->update(['pid'=>$pid]);
            
            $this->success(lang('Delete success!'), url('manager/index'));
        }else{
            $this->error(lang('Delete failed!'));
        }
    }
}
