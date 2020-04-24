<?php

namespace app\index\controller;


use app\common\validate\MemberValidate;
use shirne\common\ValidateHelper;
use shirne\third\Aliyun;
use think\Db;

/**
 * Class MemberController
 * @package app\index\controller
 */
class MemberController extends AuthedController
{
    public function initialize()
    {
        parent::initialize();
        $this->assign('navmodel','member');
    }

    /**
     * 会员中心
     */
    public function index(){
        $this->initLevel();

        $this->assign('userLevel',$this->userLevel);
        return $this->fetch();
    }

    /**
     * 个人资料
     */
    public function profile(){
        if($this->request->isPost()){
            $data=$this->request->only(['realname','email','mobile','gender','birth','qq','wechat','alipay'],'post');
            if(!empty($data['birth']) && $data['birth']!='') {
                $data['birth'] = strtotime($data['birth']);
            }else{
                unset($data['birth']);
            }
            $validate=new MemberValidate();
            $validate->setId($this->userid);
            if(!$validate->scene('edit')->check($data)){
                $this->error($validate->getError());
            }else{
                $data['id']=$this->userid;
                Db::name('Member')->update($data);
                user_log($this->userid,'addressadd',1,'修改个人资料');
                $this->success('保存成功',aurl('index/member/profile'));
            }
        }

        return $this->fetch();
    }

    public function password(){
        if($this->request->isPost()){
            $password=$this->request->post('password');
            if(!compare_password($this->user,$password)){
                $this->error('密码输入错误');
            }

            $newpassword=$this->request->post('newpassword');
            $salt=random_str(8);
            $data=array(
                'password'=>encode_password($newpassword,$salt),
                'salt'=>$salt
            );
            Db::name('Member')->where('id',$this->userid)->update($data);
            $this->success('密码修改成功',aurl('index/member/index'));
        }

        return $this->fetch();
    }

    /**
     * 修改头像
     */
    public function avatar(){
        if($this->request->isPost()){
            $data=[];
            $uploaded=$this->upload('avatar','upload_avatar');
            if(empty($uploaded)){
                $this->error('请选择文件');
            }
            $data['avatar']=$uploaded['url'];
            $result=Db::name('Member')->where('id',$this->userid)->update($data);
            if($result){
                if(!empty($this->user['avatar']))delete_image($this->user['avatar']);
                user_log($this->userid, 'avatar', 1, '修改头像');
                $this->success('更新成功',aurl('index/member/avatar'));
            }else{
                $this->error('更新失败');
            }
        }
        return $this->fetch();
    }


    /**
     * 安全中心
     */
    public function security(){
        return $this->fetch();
    }

    public function sendcode($password, $mobile='', $email=''){
        if(empty($mobile) && empty($email)){
            $this->error('参数错误');
        }
        if(empty($password)){
            $this->error('请填写当前密码');
        }
        if(encode_password($password,$this->user['salt']) !== $this->user['password']){
            $this->error('密码验证失败');
        }
        $mcktimes = (int)cache('verify_code_times');
        if($mcktimes > 5){
            $this->error('系统繁忙');
        }
        // 发送随机验证码
        $randcode = random_str(6,'number');
        if(!empty($email)){
            if(!ValidateHelper::isEmail($email)){
                $this->error('邮箱格式错误');
            }
            session('email_code',$randcode);
            session('bind_email',$email);
            $result = $this->sendEmail($email,'绑定邮箱','您正在进行邮箱绑定操作，本次验证码 ['.$randcode.'] , 如非本人操作请忽略');
            cache('verify_code_times',++$mcktimes);
            if($result){
                $this->success('验证码已发送');
            }else{
                $this->error('验证码发送失败');
            }
        }else{
            if(!ValidateHelper::isMobile($mobile)){
                $this->error('手机号码格式错误');
            }
            session('mobile_code',$randcode);
            session('bind_mobile',$mobile);
            $tplCode = getSetting('aliyun_dysms_verify');
            if(empty($tplCode)){
                $this->error('验证码发送失败');
            }
            $aliyun = new Aliyun($this->config);
            $result = $aliyun->sendSms($mobile, $randcode, $tplCode, getSetting('aliyun_dysms_sign'));
            cache('verify_code_times',++$mcktimes);
            if($result){
                $this->success('验证码已发送');
            }else{
                $this->error('验证码发送失败');
            }
        }
    }

    public function bind($verifycode, $type='mobile') {
        if(empty($verifycode)){
            $this->error('请填写验证码');
        }
        if($type == 'mobile'){
            $code = session('mobile_code');
            $mobile = session('bind_mobile');
            if(empty($code) || empty($mobile)){
                $this->error('验证码已失效');
            }
            if($verifycode !== $code){
                $this->error('验证码错误');
            }
            Db::name('member')->where('id',$this->userid)->update([
                'mobile'=>$mobile,
                'mobile_bind'=>1
            ]);
            $this->success('绑定成功');
        }elseif($type == 'email'){
            $code = session('email_code');
            $email = session('bind_email');
            if(empty($code) || empty($email)){
                $this->error('验证码已失效');
            }
            if($verifycode !== $code){
                $this->error('验证码错误');
            }
            Db::name('member')->where('id',$this->userid)->update([
                'email'=>$email,
                'email_bind'=>1
            ]);
            $this->success('绑定成功');
        }
        $this->error('操作失败');
    }

    public function notice(){
        $notices=Db::name('notice')->order('id desc')->paginate(10);
        $this->assign('notices',$notices);
        $this->assign('page',$notices->render());
        return $this->fetch();
    }

    public function feedback(){
        $unreplyed=Db::name('feedback')->where(array('member_id'=>$this->userid,'reply_time'=>0))->count();
        if($this->request->isPost()){
            if($unreplyed>0)$this->error('您的反馈尚未回复');
            $content=$this->request->post('content');
            $data=array();
            $data['content']=htmlspecialchars($content);
            $data['member_id']=$this->userid;
            $data['type']=1;
            $data['create_time']=time();
            $data['ip']=$this->request->ip();
            $data['status']=0;
            $data['reply_time']=0;
            $feedid=Db::name('feedback')->insert($data);
            if($feedid){
                $this->success('反馈成功');
            }else{
                $this->error('系统错误');
            }
        }
        $feedbacks=Db::name('feedback')->where('member_id',$this->userid)->order('id desc')->paginate(10);

        $this->assign('feedbacks',$feedbacks);
        $this->assign('page',$feedbacks->render());
        $this->assign('unreplyed',$unreplyed);
        return $this->fetch();
    }

    public function logout(){
        $this->clearLogin();
        $this->success('退出成功',url('index/login/index'));
    }
}