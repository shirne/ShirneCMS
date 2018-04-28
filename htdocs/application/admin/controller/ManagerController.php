<?php
namespace app\admin\controller;


/**
 * 管理员管理
 */
class ManagerController extends BaseController
{
    public function _initialize(){
        $this->table='Manager';
        $this->model=M($this->table);
        parent::_initialize();
    }

    /**
     * 用户列表
     */
    public function index($key="")
    {
        $where=array();
        if(!empty($key )){
            $where['username'] = array('like',"%$key%");
            $where['email'] = array('like',"%$key%");
            $where['_logic'] = 'or';
        }

        $this->pagelist($this->model,$where,'id ASC');

        $this->display();
    }

    public function log($key=''){
        $model=D('ManagerLogView');
        $where=array();
        if(!empty($key)){

        }

        $count  = $model->where($where)->count();// 查询满足要求的总记录数
        $Page = new \Extend\Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();// 分页显示输出
        $logs = $model->where($where)->limit($Page->firstRow.','.$Page->listRows)->order('manager_log.id DESC')->select();
        $this->assign('logs', $logs);
        $this->assign('page',$show);
        $this->display();
    }
    public function logview(){
        $id=I('id/d');
        $model=D('ManagerLogView');

        $m=$model->where("manager_log.id= %d",$id)->find();

        $this->assign('m', $m);
        $this->display();
    }

    public function logclear(){
        $date=I('date');
        $d=strtotime($date);
        if(empty($d)){
            $date=date_sub(new \DateTime(date('Y-m-d')),new \DateInterval('P1M'));
            $d=$date->getTimestamp();
        }

        $model=D('manager_log');

        $model->where(array("create_at"=>array('ELT',$d)))->delete();
        user_log($this->mid,'clearlog',1,'清除日志' ,'manager');
        $this->success("清除完成");
    }

    /**
     * 添加用户
     */
    public function add()
    {
        //默认显示添加表单
        if (!IS_POST) {
            $this->display();
        }
        if (IS_POST) {
            //如果用户提交数据
            $model = D($this->table);
            $data=$model->create();
            if (empty($data)) {
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                $this->error($model->getError());
                exit();
            } else {
                $data['salt']=random_str(8);
                $data['password']=encode_password($data['password'],$data['salt']);
                $data['last_view_member']=time();
                if ($model->add($data)) {
                    $this->success("添加成功", U('manager/index'));
                } else {
                    $this->error("添加失败");
                }
            }
        }
    }
    /**
     * 更新管理员信息
     */
    public function update($id)
    {
        $id=intval($id);
        if($id==0)$this->error('参数错误');
        //默认显示添加表单
        if (!IS_POST) {
            $model = $this->model->find($id);
            $this->assign('model',$model);
            $this->display();
        }
        if (IS_POST) {
            $model = D($this->table);
            if (!$model->create()) {
                $this->error($model->getError());
            }else{
                //验证密码是否为空   
                $data = I('post.');
                if(!empty($data['password'])){
                    $data['salt']=random_str(8);
                    $data['password'] = encode_password($data['password'],$data['salt']);
                }else{
                    unset($data['password']);
                }
                //强制更改超级管理员用户类型
                if(C('SUPER_ADMIN_ID') ==$id){
                    $data['type'] = 1;
                }
                //更新
                if ($model->save($data)) {
                    $this->success("更新成功", U('manager/index'));
                } else {
                    $this->error("未做任何修改,更新失败");
                }        
            }
        }
    }

    /**
     * 管理员权限
     */
    public function permision($id){
        $id=intval($id);
        if($id==0)$this->error('参数错误');
        $model = M('manager_permision')->where(array('manager_id'=>$id))->find();
        if(empty($model)){
            $model=array();
            $model['manager_id']=$id;
            $model['global']='';
            $model['detail']='';
            $model['id']=M('manager_permision')->add($model);
        }
        if(IS_POST){
            $model['global']=$_POST['global'];
            if(!is_array($model['global']))$model['global']=array();
            $model['global']=implode(',',$model['global']);
            $model['detail']=$_POST['detail'];
            if(!is_array($model['detail']))$model['detail']=array();
            $model['detail']=implode(',',$model['detail']);
            if(M('manager_permision')->save($model)){
                $this->success("更新成功", U('manager/index'));
            }else {
                $this->error("未做任何修改,更新失败");
            }
        }
        $model['global']=explode(',',$model['global']);
        $model['detail']=explode(',',$model['detail']);
        $this->assign('model',$model);
        $this->assign('perms',include(APP_PATH.BIND_MODULE.'/Conf/permisions.php'));
        $this->display();
    }
    /**
     * 删除管理员
     */
    public function delete($id)
    {
    	$id = intval($id);
    	if(C('SUPER_ADMIN_ID') == $id) $this->error("超级管理员不可禁用!");

        //查询status字段值
        $result = $this->model->find($id);
        //更新字段
        $data['id']=$id;
        if($result['status'] == 1){
        	$data['status']=0;
        }
        if($result['status'] == 0){
        	$data['status']=1;
        }
        if($this->model->save($data)){
            $this->success("状态更新成功", U('manager/index'));
        }else{
            $this->error("状态更新失败");
        }
    }
}
