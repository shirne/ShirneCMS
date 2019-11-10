<?php

namespace app\api\controller;

use app\common\model\MemberModel;
use app\common\validate\MemberValidate;
use app\api\facade\MemberTokenFacade;
use extcore\traits\Upload;
use think\Db;
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
        $profile['level']=$levels[$profile['level_id']]?:[];
        return $this->response($profile);
    }

    public function update_profile(){
        $data=$this->request->only(['realname','email','mobile','gender','birth','qq','wechat','alipay'],'put');
        if(!empty($data['birth']) && $data['birth']!='') {
            $data['birth'] = strtotime($data['birth']);
        }else{
            unset($data['birth']);
        }
        $validate=new MemberValidate();
        $validate->setId($this->user['id']);
        if(!$validate->scene('edit')->check($data)){
            $this->error($validate->getError(),0);
        }else{
            $data['id']=$this->user['id'];
            Db::name('Member')->update($data);
            user_log($this->user['id'],'addressadd',1,'修改个人资料');
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