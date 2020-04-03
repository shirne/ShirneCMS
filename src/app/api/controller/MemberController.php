<?php

namespace app\api\controller;

use app\common\model\MemberModel;
use app\common\validate\MemberValidate;
use app\api\facade\MemberTokenFacade;
use app\common\model\MemberAgentModel;
use app\common\model\MemberLevelLogModel;
use app\common\model\MemberLevelModel;
use extcore\traits\Upload;
use think\facade\Db;
use think\Loader;

/**
 * 会员操作接口
 * Class MemberController
 * @package app\api\Controller
 */
class MemberController extends AuthedController
{
    use Upload;

    public function profile($agent=''){
        $profile=Db::name('member')
            ->hidden('password,salt,sec_password,sec_salt,delete_time')
            ->where('id',$this->user['id'])
            ->find();
        
        if(!empty($agent)){
            MemberModel::autoBindAgent($profile,$agent);
        }
        
        $levels = getMemberLevels();
        $profile['level']=$levels[$profile['level_id']] ?? new \stdClass();
        $agents = MemberAgentModel::getCacheData();
        $profile['agent'] = $agents[$profile['is_agent']] ?? new \stdClass();
        return $this->response($profile);
    }

    public function update_profile(){
        $data=$this->request->only(['username','nickname','realname','email','mobile','gender','birth','qq','wechat','alipay','province','city','county','address'],'put');
        if(isset($data['username'])){
            if(strpos($this->user['username'],'#')===false){
                $this->error('登录名不可修改');
            }
            if(empty($data['username'])){
                $this->error('要修改的用户名不能为空');
            }
            if(!preg_match('/^[a-zA-Z][A-Za-z0-9\-\_]{5,19}$/',$data['username'])){
                $this->error('用户名格式不正确');
            }
            $exists= Db::name('member')->where('username',$data['username'])->find();
            if(!empty($exists)){
                $this->error('用户名已存在');
            }
        }
        if(!empty($data['birth'])) {
            $data['birth'] = strtotime($data['birth']);
        }else{
            if(isset($data['birth'])) unset($data['birth']);
        }
        $validate=new MemberValidate();
        $validate->setId($this->user['id']);
        if(!$validate->scene('edit')->check($data)){
            $this->error($validate->getError(),0);
        }else{
            $data['id']=$this->user['id'];
            Db::name('Member')->update($data);
            user_log($this->user['id'],'update_profile',1,'修改个人资料');
            $this->success('保存成功');
        }
    }

    public function avatar(){
        $data=[];
        $uploaded=$this->upload('avatar','upload_avatar');
        if(empty($uploaded)){
            $this->error('请选择文件',0);
        }
        $data['avatar']=$uploaded['url'];
        $result=Db::name('Member')->where('id',$this->user['id'])->update($data);
        if($result){
            if(!empty($this->user['avatar']))delete_image($this->user['avatar']);
            user_log($this->user['id'], 'avatar', 1, '修改头像');
            $this->success('更新成功');
        }else{
            $this->error('更新失败',0);
        }
    }

    public function uploadImage(){
        $uploaded=$this->upload('member','file_upload');

        return $this->response([
            'url'=>$uploaded['url']
        ]);
    }

    public function upgrade(){
        $target = $this->request->post('level_id');
        $balance_pay = $this->request->post('balance_pay') == '1';
        $levels = MemberLevelModel::getCacheData();
        if($target<=0 || !isset($levels[$target])){
            $this->error('升级级别错误',0);
        }

        $curLevel = $this->user['level_id']?$levels[$this->user['level_id']]:null;
        $level = $levels[$target];
        if(!$curLevel || $curLevel['sort'] >= $level['sort']){
            $this->error('升级级别错误',0);
        }

        if($level['upgrade_type'] == 0){
            $this->error('该等级不可申请',0);
        }

        $model = new MemberLevelLogModel();
        $insert_id = $model->makeOrder($this->user, $level, '', $balance_pay );

        if($insert_id === false){
            $this->error($model->getError());
        }
        if($balance_pay){
            $this->success('开通成功');
        }else{
            $this->success('UL_'.$insert_id, 1, '申请已提交');
        }
    }
    
    public function change_password(){
        $password=$this->request->post('password');
        if(!compare_password($this->user,$password)){
            $this->error('密码输入错误',0);
        }
        
        $newpassword=$this->request->post('newpassword');
        $salt=random_str(8);
        $data=array(
            'password'=>encode_password($newpassword,$salt),
            'salt'=>$salt
        );
        Db::name('Member')->where('id',$this->user['id'])->update($data);
        $this->success('密码修改成功');
    }

    public function sec_password(){
        $password=$this->request->post('password');
        if(empty($this->user['secpassword'])){
            if(!compare_password($this->user,$password)){
                $this->error('当前密码输入错误',0);
            }
        }else{
            if(!compare_secpassword($this->user,$password)){
                $this->error('安全密码输入错误',0);
            }
        }
        
        $newpassword=$this->request->post('newpassword');
        $salt=random_str(8);
        $data=array(
            'secpassword'=>encode_password($newpassword,$salt),
            'secsalt'=>$salt
        );
        Db::name('Member')->where('id',$this->user['id'])->update($data);
        $this->success('安全密码修改成功');
    }

    public function search($keyword){
        if(empty($keyword)){
            $this->error('请输入会员名或手机号');
        }
        $result = Db::name('member')->where('id|username|mobile',$keyword)->find();
        if(empty($result)){
            $this->error('未搜索到会员');
        }
        return $this->response([
            'id'=>$result['id'],
            'username'=>$result['username'],
            'nickname'=>$result['username'],
            'realname'=>$result['realname'],
            'mobile'=>$result['mobile'],
            'avatar'=>$result['avatar'],
        ]);
    }
    
    public function quit(){
        if($this->isLogin){
            MemberTokenFacade::clearToken($this->token);
        }
        $this->success('退出成功');
    }

    public function addresses(){
        return action('member.address/index');
    }

    public function get_address($id){
        return action('member.address/view',['id'=>$id]);
    }
    public function edit_address($id=0){
        return action('member.address/save',['id'=>$id]);
    }
    public function del_address($id){
        return action('member.address/delete',['id'=>$id]);
    }
    public function set_default_address($id){
        return action('member.address/set_default',['id'=>$id]);
    }

    public function orders($status=''){
        return action('member.order/index',['status'=>$status]);
    }

    public function order_view($id){
        return action('member.order/view',['id'=>$id]);
    }

    public function favourite($type){
        return action('member.favourite/index',['type'=>$type]);
    }

    public function add_favourite($type,$id){
        return action('member.favourite/add',['type'=>$type,'id'=>$id]);
    }

    public function del_favourite($type,$ids){
        return action('member.favourite/remove',['type'=>$type,'ids'=>$ids]);
    }
}