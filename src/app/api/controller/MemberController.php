<?php

namespace app\api\controller;

use app\common\model\MemberModel;
use app\common\validate\MemberValidate;
use app\api\facade\MemberTokenFacade;
use app\common\model\MemberAgentModel;
use app\common\model\MemberLevelLogModel;
use app\common\model\MemberLevelModel;
use app\common\service\CheckcodeService;
use Exception as GlobalException;
use extcore\traits\Upload;
use think\facade\Db;
use think\Loader;
use think\response\Json;
use Throwable;

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
            ->hidden(explode(',','password,salt,sec_password,sec_salt,delete_time'))
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

    /**
     * 登记手机号
     * @param string $mobile 
     * @param string $code 
     * @param string $nickname 
     * @param string $areas 
     * @return void 
     */
    public function mobile_register($mobile='', $code='', $nickname='', $areas=''){
        if(empty($mobile) || empty($code)){
            $this->error('请填写手机号及验证码');
        }
        $unbindKey = 'unbind_'.$this->user['mobile'].'_'.$this->user['id'];
        if(!ValidateHelper::isMobile($mobile)){
            $this->error('手机号码错误');
        }
        
        if($this->user['mobile_bind']){
            $unbined = cache($unbindKey);
            if(empty($unbined)){
                $this->error('请先解绑旧手机号码');
            }
        }
        $service = new CheckcodeService();
        $result = $service->verifyCode($mobile, $code);
        if(!$result){
            $this->error('验证码错误');
        }
        $data=[
            'mobile'=>$mobile,
            'mobile_bind'=>1
        ];
        if(!empty($nickname)){
            $data['nickname']=$nickname;
        }
        if(!empty($areas)){
            if(!is_array($areas)){
                $areas = explode('/',$areas);
            }
            $data['province']=$areas[0];
            $data['city']=$areas[1];
            $data['county']=$areas[2];
        }
        Db::name('Member')->where('id',$this->user['id'])->update($data);
        cache($unbindKey, NULL);
        user_log($this->user['id'],'update_mobile',1,'绑定手机号');
        $this->user = Db::name('Member')->where('id',$this->user['id'])->find();

        // 绑定手机号码升级为初级代言人
        $seted = MemberModel::autoUpdateBeginner($this->user);

        $this->success(['is_set_agent'=>($seted==2)?1:0,'image'=>getSetting('beginner_reward_image')],1,'绑定成功');
    }

    /**
     * 绑定手机号
     * @param mixed $mobile 
     * @param mixed $code 
     * @param int $step 
     * @return void 
     */
    public function bind_mobile($mobile, $code, $step = 0){
        $unbindKey = 'unbind_'.$this->user['mobile'].'_'.$this->user['id'];
        if(!ValidateHelper::isMobile($mobile)){
            $this->error('手机号码错误');
        }
        
        if($step == 0){
            if(!$this->user['mobile_bind']){
                $this->error('未绑定手机');
            }
            if($mobile != $this->user['mobile']){
                $this->error('手机号码非绑定的手机号');
            }
            $service = new CheckcodeService();
            $result = $service->verifyCode($mobile, $code);
            if($result){
                cache($unbindKey, 1, ['expire'=>10*60]);
                $this->success('验证通过');
            }
            $this->error('验证失败');
        }else{
            if($this->user['mobile_bind']){
                $unbined = cache($unbindKey);
                if(empty($unbined)){
                    $this->error('请先解绑旧手机号码');
                }
            }
            $service = new CheckcodeService();
            $result = $service->verifyCode($mobile, $code);
            if(!$result){
                $this->error('验证码错误');
            }
            Db::name('Member')->where('id',$this->user['id'])->update([
                'mobile'=>$mobile,
                'mobile_bind'=>1
            ]);
            cache($unbindKey, NULL);
            user_log($this->user['id'],'update_mobile',1,'修改绑定手机号');
            $this->user = Db::name('Member')->where('id',$this->user['id'])->find();
            
            // 绑定手机号码升级为初级代言人
            $seted = MemberModel::autoUpdateBeginner($this->user);

            $this->success(['is_set_agent'=>($seted==2)?1:0,'image'=>getSetting('beginner_reward_image')],1,'绑定成功');
        }
    }

    /**
     * 发送用于绑定的验证码
     * @param string $mobile 
     * @return void 
     */
    public function smscode($mobile='')
    {
        //绑定手机号
        if(!empty($mobile)){
            
            if(! ValidateHelper::isMobile($mobile)){
                $this->error('手机号码格式错误');
            }
        }else{
            $mobile = $this->user['mobile'];
            if(empty($mobile)){
                $this->error('您的账号未绑定手机号码');
            }
        }

        $service = new CheckcodeService();
        $result = $service->sendCode('mobile', $mobile, 'verify');
        if(!$result){
            $this->error($service->getError());
        }

        $this->success('验证码已发送');
    }

    /**
     * 更新头像
     * @return void 
     */
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

    /**
     * 上传会员图片，如：头像图
     * @return Json 
     * @throws GlobalException 
     */
    public function uploadImage(){
        $uploaded=$this->upload('member','file_upload');

        return $this->response([
            'url'=>$uploaded['url']
        ]);
    }

    /**
     * 升级申请
     * @return void 
     */
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
    
    /**
     * 修改密码
     * @return void 
     */
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

    /**
     * 修改二级密码
     * @return void 
     */
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

    /**
     * 精确搜索会员资料
     * @param string $keyword 会员名或手机号
     * @return Json 
     */
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
    
    /**
     * 退出登录，清除token
     * @return void 
     */
    public function quit(){
        if($this->isLogin){
            MemberTokenFacade::clearToken($this->token);
        }
        $this->success('退出成功');
    }

}