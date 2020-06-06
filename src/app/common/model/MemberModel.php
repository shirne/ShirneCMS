<?php
namespace app\common\model;

use app\common\core\BaseModel;
use app\common\service\MessageService;
use shirne\common\Poster;
use think\facade\Db;
use think\facade\Log;

/**
 * Class MemberModel
 * @package app\common\model
 */
class MemberModel extends BaseModel
{
    protected $name = 'member';
    protected $insert = ['is_agent' => 0,'type'=>1,'status'=>1,'referer'];
    protected $autoWriteTimestamp = true;

    protected function setRefererAttr($value=0)
    {
        return intval($value);
    }

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
        if(strcmp($this['id'], $referer)===0 || strcmp($this['agentcode'],$referer)===0){
            $this->setError('不能将会员设为自己的推荐人');
            return false;
        }
        if(strcmp($this['referer'], $referer) === 0){
            return true;
        }
        $rmember=Db::name('member')->where('id|agentcode',$referer)->find();
        if(empty($rmember) || !$rmember['is_agent']){
            $this->setError('设置的推荐人不是代理');
            return false;
        }
        if(strcmp($this['referer'], $rmember['id'])===0){
            return true;
        }
        
        $parents = static::getParents($rmember['id'], 0);
        if(in_array($this['id'], $parents)){
            $this->setError('推荐人关系冲突');
            return false;
        }
        if($this['referer']>0){
            Db::name('member')->where('id',$this['referer'])->dec('recom_total',1);
            if($this['is_agent']){
                Db::name('member')->where('id',$this['referer'])->dec('recom_count',1);
                $mparents=static::getParents($this['referer'],0);
                $mparents = array_unshift($mparents, $this['referer']);
                Db::name('member')->whereIn('id',$mparents)->dec('team_count',1);
            }
        }
        $this->save(['referer'=>$rmember['id']]);

        Db::name('member')->where('id',$this['referer'])->inc('recom_total',1);
        if($this['is_agent']){
            static::updateRecommend($this['referer']);
        }

        static::sendBindAgentMessage($this, $rmember);

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
            Db::name('member')->where('id',$referer)->dec('recom_total',1);
            if($this['is_agent']){
                Db::name('member')->where('id',$referer)->dec('recom_count',1);
                $parents=static::getParents($referer,0);
                array_unshift($parents,$referer);
                Db::name('member')->whereIn('id',$parents)->dec('team_count',1);
            }
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
            self::autoCheckUpgrade($parents);

        }
    }
    
    private static function autoCheckUpgrade($parents){
        $agents = MemberAgentModel::getCacheData();
        $needUpdates=Db::name('member')->whereIn('id',$parents)
            ->field('id,level_id,is_agent,team_count,recom_count,recom_total,recom_performance,total_performance,agentcode,referer')
            ->select();
        foreach($needUpdates as $parent){
            if(!$parent['is_agent']){
                continue;
            }
            foreach($agents as $agent){
                if($parent['is_agent'] < $agent['id'] &&
                    $parent['team_count']>=$agent['team_count'] &&
                    $parent['recom_count']>=$agent['recom_count'] &&
                    $parent['recom_performance']>=$agent['recom_performance'] &&
                    $parent['total_performance']>=$agent['total_performance']){
                    
                    self::setAgent($parent,$agent['id'],'auto');
                    break;
                }
            }
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
            $data['agentcode']=random_str(8,'string',1);
            while(Db::name('member')->where('agentcode',$data['agentcode'])->count()>0){
                $data['agentcode']=random_str(8,'string',1);
            }
        }elseif($member['is_agent'] == $agent_id){
            return true;
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

            if($member['is_agent'] < 1){
                self::sendBecomeAgentMessage($member);
            }elseif($member['is_agent'] > 0){
                self::sendUpgradeAgentMessage($member, $agent);
            }
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
            if(!empty($parents)){
                Db::name('member')->where('id',$parents[0])->dec('recom_count',1);
                Db::name('member')->whereIn('id',$parents)->dec('team_count',1);
            }
        }
        return $count;
    }

    protected $memberLevel;
    public function getLevel(){
        if(!$this->memberLevel && $this->level_id){
            $levels = MemberLevelModel::getCacheData();
            $this->memberLevel = $levels[$this->level_id];
        }
        if(empty($this->memberLevel)){
            $this->memberLevel=[
                'level_id'=>0,
                'level_name'=>'默认会员'
            ];
        }
        return $this->memberLevel;
    }
    public function getLevelName(){
        $this->getLevel();
        return $this->memberLevel['level_name'];
    }
    protected $memberAgent;
    public function getAgent(){
        if(!$this->memberAgent && $this->is_agent){
            $agents = MemberAgentModel::getCacheData();
            $this->memberAgent = $agents[$this->is_agent];
        }
        if(empty($this->memberAgent)){
            $this->memberAgent=[
                'id'=>0,
                'name'=>''
            ];
        }
        return $this->memberAgent;
    }
    public function getAgentName(){
        $this->getAgent();
        return $this->memberAgent['name'];
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
        $user=Db::name('Member')->where('id',$currentid)->field('id,level_id,is_agent,username,referer')->find();
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
            $user=Db::name('Member')->where('id',$currentid)->field('id,level_id,is_agent,username,referer')->find();
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
        $users=Db::name('Member')->where('referer',$userid)->field('id,level_id,is_agent,username,referer')->select()->all();
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
            $users=Db::name('Member')->whereIn('referer',$userids)->field('id,level_id,is_agent,username,referer')->select()->all();
        }
        return $sons;
    }
    
    public static function autoBindAgent($member,$agent){
        if(empty($agent) || empty($member)){
            return false;
        }
        if(!is_array($member) && is_numeric($member)){
            $member = static::where('id',$member)->find();
            if(empty($member))return false;
        }
        if($member['is_agent'] || strcmp($member['agentcode'],$agent)===0 || strcmp($member['id'], $agent)===0 ){
            return false;
        }
        if(strcmp($member['referer'], $agent) === 0){
            return true;
        }
        
        $agentMember=static::where('agentcode|id',$agent)
            ->where('is_agent','>',0)
            ->where('status',1)->find();
        if(empty($agentMember) || $agentMember['id']==$member['id'] || !$agentMember['is_agent']){
            return false;
        }
        if(strcmp($member['referer'], $agentMember['id']) === 0){
            return true;
        }
        
        static::update(['referer'=>$agentMember['id']],array('id'=>$member['id']));

        Db::name('member')->where('id',$agentMember['id'])->setInc('recom_total',1);

        static::sendBindAgentMessage($member, $agentMember);

        return true;
    }

    public static function showname($member){
        if(!empty($member['nickname'])){
            return $member['nickname'];
        }
        if(!empty($member['username'])){
            return $member['username'];
        }
        if(!empty($member['mobile'])){
            return $member['mobile'];
        }
        return '[匿名]';
    }

    public static function sendBindAgentMessage($member, $agent){
        $message = getSetting('message_bind_agent');
        if(!empty($message)){
            foreach([
                'username'=>static::showname($member),
                'agent'=>static::showname($agent),
                'userid'=>$member['id'],
                'agentid'=>$agent['id']
            ] as $k=>$v){
                $message = str_replace("[$k]", $v, $message);
            }

            MessageService::sendWechatMessage($agent['id'],$message);
        }
    }

    public static function sendBecomeAgentMessage($member){
        $message = getSetting('message_become_agent');
        if(!empty($message)){
            foreach([
                'username'=>static::showname($member),
                'userid'=>$member['id'],
            ] as $k=>$v){
                $message = str_replace("[$k]", $v, $message);
            }

            MessageService::sendWechatMessage($member['id'],$message);
        }
    }

    public static function sendUpgradeAgentMessage($member, $agent){
        $message = getSetting('message_upgrade_agent');
        if(!empty($message)){
            foreach([
                'username'=>static::showname($member),
                'userid'=>$member['id'],
                'agent'=>$agent['name']
            ] as $k=>$v){
                $message = str_replace("[$k]", $v, $message);
            }

            MessageService::sendWechatMessage($member['id'],$message);
        }
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

    

    public function getSharePoster($platform, $page, $force=false){
        if(!$this->isExists()){
            $this->setError('会员资料未初始化');
            return false;
        }
        if(empty($this['agentcode'])){
            $this->setError('会员不是代理');
            return false;
        }
        $sharepath = './uploads/share/'.($this['id']%100).'/'.$this['agentcode'].'-'.$platform.'.jpg';
        
        $config=$this->get_poster_config();
        if(empty($config) || empty($config['background'])){
            $this->setError('请配置海报生成样式');
            return false;
        }
        if(!file_exists($config['background'])){
            $this->setError('请设置海报背景图');
            return false;
        }
        if(file_exists($sharepath) && !$force){
            $fileatime=filemtime($sharepath);
            if($this['update_time'] - $fileatime < 7*24*60*60 &&
                filemtime($config['background']) < $fileatime
            ){
                return media(ltrim($sharepath,'.'));
            }
        }
        if(in_array($platform, ['wechat-miniprogram','wechat-minigame'])){
            $created = $this->create_appcode_img($config,$sharepath,$page);
        }else{
            $created = $this->create_share_img($config,$sharepath,$page);
        }
        if(!$created)return $created;
        return media(ltrim($sharepath,'.'));
    }

    private function get_poster_config(){
        $config = [];
        $sysconfig=getSettings(false,true);
        $posterConfig=$sysconfig['poster'];
        if(empty($posterConfig) || empty($posterConfig['poster_background'])){
            return false;
        }

        $config['background']='.'.$posterConfig['poster_background'];
        $config['data']['avatar']=$posterConfig['poster_avatar'];
        $config['data']['avatar']['type']='image';
        $config['data']['nickname']=$posterConfig['poster_nickname'];
        $config['data']['qrcode']=$posterConfig['poster_qrcode'];
        $config['data']['qrcode']['type']='image';
        if(!empty($posterConfig['poster_qrlogo'])){
            $config['data']['qrlogo']=$posterConfig['poster_qrcode'];
            $config['data']['qrlogo']['type']='image';
            $config['data']['qrlogo']['value']='.'.$posterConfig['poster_qrlogo'];
        }
        return $config;
    }

    private function create_share_img($config,$sharepath,$page){
        $qrpath=dirname($sharepath);
        $qrfile = $this['agentcode'].'-qrcode.png';
        $filename=$qrpath.'/'.$qrfile;

        if(!file_exists($filename)) {
            $content=gener_qrcode($page, 430);
            if(!is_dir($qrpath)){
                mkdir($qrpath,0777,true);
            }
            file_put_contents($filename,$content);
            if(!file_exists($filename)){
                $this->setError('二维码生成失败');
                return false;
            }
        }
        
        //$config['background']=$bgpath;
        $poster = new Poster($config);
        $poster->generate([
            'qrcode'=>$filename,
            'avatar'=>$this['avatar'],
            'bg'=>1,
            'nickname'=>$this['nickname']
        ]);
        $poster->save($sharepath);
        return true;
    }
    private function create_appcode_img($config,$sharepath,$page){
        $appid=$this->request->tokenData['appid'];
        $wechat=WechatModel::where('appid',$appid)->find();
        if(empty($wechat)){
            $this->setError('分享图生成失败(wechat)');
            return false;
        }
    
        $qrpath=dirname($sharepath);
        $qrfile = $this['agentcode'].'-appcode.png';
        $filename=$qrpath.'/'.$qrfile;
        if(!file_exists($filename)) {
            $app = WechatModel::createApp($wechat);
            if (empty($app)) {
                $this->setError('分享图生成失败(app)');
                return false;
            }
    
            $response = $app->app_code->getUnlimit('agent=' . $this['agentcode'], [
                'page' => $page,
                'width' => 520
            ]);
            if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
                $genername = $response->saveAs($qrpath, $qrfile);
            }
            if(empty($genername)){
                $this->setError('小程序码生成失败');
                return false;
            }
        }
        
        //$config['background']=$bgpath;
        $poster = new Poster($config);
        $poster->generate([
            'appcode'=>$filename,
            'avatar'=>$this['avatar'],
            'bg'=>1,
            'nickname'=>$this['nickname']
        ]);
        $poster->save($sharepath);
        return true;
    }
}