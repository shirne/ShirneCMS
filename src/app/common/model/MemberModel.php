<?php
namespace app\common\model;

use app\common\core\BaseModel;
use think\facade\Db;
use think\facade\Log;

/**
 * Class MemberModel
 * @package app\common\model
 */
class MemberModel extends BaseModel
{
    protected $name = 'member';
    protected $insert = ['is_agent' => 0,'type'=>1,'status'=>1,'referer'=>0];
    protected $autoWriteTimestamp = true;

    public function onAfterUpdate ($model) {
        $users=$model->where($model->getWhere())->select();
        //代理会员组
        if(!empty($users)) {
            $levels = getMemberLevels();
            foreach ($users as $user) {
                //代理会员组
                if (!$user['is_agent'] && $user['level_id'] > 0) {
                    if (!empty($levels[$user['level_id']]) && $levels[$user['level_id']]['is_agent']) {
                        self::checkAgent($user);
                    }
                }
            }
        }
    }

    public function onAfterInsert ( $model) {
        if ($model['referer']) {
            Db::name('member')->where('id',$model->referer)->inc('recom_total',1);
        }
        if ($model['level_id']) {
            $levels = getMemberLevels();
            if (!$model['is_agent'] ) {
                if (!empty($levels[$model['level_id']]) && $levels[$model['level_id']]['is_agent']) {
                    self::checkAgent($model);
                }
            }
        }
    }

    public function setReferer($referer){
        if(!$this['id']){
            $this->setError('会员未初始化');
            return false;
        }
        if(empty($referer)){
            $this->setError('推荐人不存在');
            return false;
        }
        if($this['id'] == $referer || $this['agentcode']==$referer){
            $this->setError('不能将会员设为自己的推荐人');
            return false;
        }
        $rmember=Db::name('member')->where('id|agentcode',$referer)->find();
        if(empty($rmember) || !$rmember['is_agent']){
            $this->setError('设置的推荐人不是代理');
            return false;
        }
        
        $parents = MemberModel::getParents($rmember['id'], 0);
        if(in_array($this['id'], $parents)){
            $this->setError('推荐人关系冲突');
            return false;
        }

        $this->save(['referer'=>$rmember['id']]);
        return true;
    }

    public function clrReferer(){
        if(!$this['id']){
            $this->setError('会员未初始化');
            return false;
        }
        $referer = $this['referer'];
        if($referer){
            $this->save(['referer'=>0]);
            Db::name('member')->where('id',$referer)->dec('recom_count',1);
            $parents=static::getParents($referer,0);
            array_unshift($parents,$referer);
            Db::name('member')->whereIn('id',$parents)->dec('team_count',1);
        }
        return true;
    }
    
    public static function checkAgent($member){
        if($member['is_agent'])return;
        if(self::setAgent($member['id'])){
            self::updateRecommend($member['referer']);
        }
    }

    /**
     * 更新代理处理
     * @param $referer
     */
    public static function updateRecommend($referer){
        if($referer){
            Db::name('member')->where('id',$referer)->inc('recom_count',1);
            $parents=getMemberParents($referer,0);
            array_unshift($parents,$referer);
            Db::name('member')->whereIn('id',$parents)->inc('team_count',1);

            //代理等级自动升级

        }
    }

    /**
     * 设置代理，生成代理码
     * @param $member_id
     * @return int|string
     */
    public static function setAgent($member_id, $agent_id = 1, $type='system', $remark = ''){
        $agents = MemberAgentModel::getCacheData();
        $agent = isset($agents[$agent_id])?$agents[$agent_id]:[];
        $data=array();
        if(is_array($member_id)){
            $member = $member_id;
            $member_id = $member['id'];
        }else{
            $member = Db::name('member')->where('id',$member_id)->find();
            if(empty($member))return false;
        }
        if(empty($member['agentcode'])){
            $data['agentcode']=random_str(8);
            while(Db::name('member')->where('agentcode',$data['agentcode'])->count()>0){
                $data['agentcode']=random_str(8);
            }
        }
        $data['is_agent']=$agent_id;
        $data['update_time']=time();
        $result = Db::name('member')->where('id',$member_id)->update($data);
        if($result){
            Db::name('memberAgentLog')->insert([
                'member_id'=>$member_id,
                'agent_id'=>$agent_id,
                'type'=>$type,
                'remark'=>$remark,
                'create_time'=>time(),
            ]);
        }
        return $result;
    }

    /**
     * 取消代理,递减上级代理的推荐人数和团队人数
     * @param $member_id
     * @return int|string
     */
    public static function cancelAgent($member_id){
        $data=array();
        $data['is_agent']=0;
        $count= Db::name('member')->where('id',$member_id)->update($data);
        if($count){
            $parents=getMemberParents($member_id,0);
            Db::name('member')->where('id',$parents[0])->dec('recom_count',1);
            Db::name('member')->whereIn('id',$parents)->dec('team_count',1);
        }
        return $count;
    }

    /**
     * 获取指定层数的会员上级
     * @param $userid int 当前会员id
     * @param int $level 获取的层数
     * @param bool $getid 是否只取id
     * @return array
     */
    public static function getParents($userid,$level=5,$getid=true)
    {
        $parents=[];
        $ids=[];
        $currentid=$userid;
        $user=Db::name('Member')->where('id',$currentid)->field('id,level_id,username,referer')->find();
        $layer=0;
        while(!empty($user)){
            $layer++;
            $ids[]=$user['id'];
            $currentid=$user['referer'];
            if(!$currentid)break;
            if(in_array($currentid, $ids)!==false){
                Log::record('会员 '.$userid.' 在查找上级时在第 '.$layer.' 层出现递归',\think\Log::ERROR);
                break;
            }
            $user=Db::name('Member')->where('id',$currentid)->field('id,level_id,username,referer')->find();
            $parents[] = $getid?$currentid:$user;
            if($level>0 && $layer>=$level)break;
        }
        return $parents;
    }

    /**
     * 获取指定层数的所有下级
     * @param $userid
     * @param int $level
     * @param bool $getid
     * @return array
     */
    public static function getSons($userid,$level=1,$getid=true)
    {
        $sons=[];
        $users=Db::name('Member')->where('referer',$userid)->field('id,level_id,username,referer')->select();
        $layer=0;
        while(!empty($users)){
            $layer++;
            $userids=array_column($users,'id');
            if(in_array($userid ,$userids)){
                Log::record('会员 '.$userid.' 在查找下级时在第 '.$layer.' 层出现递归',\think\Log::ERROR);
                break;
            }
            $sons = array_merge($sons, $getid?$userids:$users);
            if($level>0 && $layer>=$level)break;
            $users=Db::name('Member')->whereIn('referer',$userids)->field('id,level_id,username,referer')->select();
        }
        return $sons;
    }
    
    public static function autoBindAgent($member,$agent){
        if(!is_array($member) && is_numeric($member)){
            $member = static::where('id',$member)->find();
            if(empty($member))return false;
        }
        if($member['is_agent'] || $member['referer'] ||
            $member['agentcode']==$agent|| $member['id']==$agent){
            return false;
        }
        
        $agentMember=static::where('agentcode|id',$agent)
            ->where('is_agent','>',0)
            ->where('status',1)->find();
        if(empty($agentMember) || $agentMember['id']==$member['id'] || !$agentMember['is_agent']){
            return false;
        }
        
        static::update(['referer'=>$agentMember['id']],array('id'=>$member['id']));
        return true;
    }

    /**
     * 从第三方授权接口的用户资料创建会员
     * @param $data
     * @param int $referer
     * @return static
     */
    public static function createFromOauth($data,$referer=0)
    {
        $data=[
            'username' => '#'.$data['openid'],
            'nickname' => $data['nickname'],
            'password' => '',
            'salt'=>'',
            'level_id'=>getDefaultLevel(),
            'gender'   => $data['gender'],
            'avatar'   => $data['avatar'],
            'referer'  => 0,
            'is_agent'=>0
        ];
        $member = self::create($data);
        if($member && !empty($member['id'])){
            $member->setReferer($referer);
        }
        return $member;
    }

    public static function checkUpdata($data, $member){
        $updata=array();
        if(!empty($data)){
            if(isset($data['gender']) && (!isset($member['gender']) || $member['gender']!=$data['gender']))$updata['gender']=$data['gender'];
            if(empty($member['province']) && !empty($data['province']))$updata['province']=$data['province'];
            if(empty($member['city']) && !empty($data['city']))$updata['city']=$data['city'];
            //if(empty($member['county']) && !empty($data['county']))$updata['county']=$data['county'];
            if(empty($member['nickname']))$updata['nickname']=$data['nickname'];
            if(empty($member['avatar']) || is_wechat_avatar($member['avatar']))$updata['avatar']=$data['avatar'];
        }
        return $updata;
    }
}