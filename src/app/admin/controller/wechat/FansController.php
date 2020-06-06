<?php

namespace app\admin\controller\wechat;

use app\common\model\MemberOauthModel;
use EasyWeChat\Kernel\Messages\Media;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\OfficialAccount\Application;
use think\facade\Db;

/**
 * 粉丝管理
 * Class FansController
 * @package app\admin\controller\wechat
 */
class FansController extends WechatBaseController
{
    public function index($keyword = ''){
        if($this->request->isPost()){
            return redirect(url('',['keyword'=>base64url_encode($keyword)]));
        }
        $keyword=empty($keyword)?"":base64url_decode($keyword);
        $model=Db::name('MemberOauth')->where('type_id',$this->wid);
        if(!empty($keyword)){
            $model->whereLike('openid|nickname',"%$keyword%");
        }
        $lists=$model->order('id desc')->paginate(15);
        $this->assign('lists',$lists);
        $this->assign('page',$lists->render());
        $this->assign('keyword',$keyword);
        
        $this->assign('support_message',$this->wechatApp instanceof Application);
        $this->assign('support_sync',$this->wechatApp instanceof Application);
        return $this->fetch();
    }
    
    public function sendmsg($openid, $msgtype, $content){
        $service = $this->wechatApp->customer_service;
        if($msgtype == 'text'){
            $messager = $service->message(new Text($content));
        }elseif($msgtype=='news') {
            $content['image'] = local_media($content['image']);
            $messager = $service->message(new News([new NewsItem($content)]));
        }elseif($msgtype=='media'){
            if($content['type']=='news' || $content['type']=='video')$content['type'] = 'mp'.$content['type'];
            $messager = $service->message(new Media($content['media_id'],$content['type']));
        }else{
            $this->error('暂时不支持的消息');
        }
    
        try{
            $result=$messager->to($openid)->send();
        }catch(\Exception $e){
            $this->apiException($e);
        }
        if(isset($result['errcode']) && $result['errcode']!=0){
            $this->error($result['errmsg']);
        }
        $this->success('消息已发送');
    }

    /**
     * 同步粉丝资料
     * @param string $openid
     * @param bool $single
     */
    public function sync($openid='',$single=0){
        $app=$this->wechatApp;
        $wechat=$this->currentWechat;

        if($single) {
            if(strpos($openid,',')===false) {
                try{
                    $user = $app->user->get($openid);
                }catch(\Exception $e){
                    $this->apiException($e);
                }
                $userData = MemberOauthModel::mapUserInfo($user);
                if(!empty($userData['unionid'])){
                    $hasMember = Db::name('MemberOauth')->where('unionid',$userData['unionid'])->where('member_id','>',0)->find();
                    if(!empty($hasMember['member_id'])){
                        $userData['member_id']=$hasMember['member_id'];
                    }
                }
                Db::name('MemberOauth')->where('openid',$openid)
                    ->update($userData);
            }else {
                try{
                    $users = $app->user->select(explode(',', $openid));
                }catch(\Exception $e){
                    $this->apiException($e);
                }
                foreach ($users['user_info_list'] as $user){
                    $userData = MemberOauthModel::mapUserInfo($user);
                    if(!empty($userData['unionid'])){
                        $hasMember = Db::name('MemberOauth')->where('unionid',$userData['unionid'])->where('member_id','>',0)->find();
                        if(!empty($hasMember['member_id'])){
                            $userData['member_id']=$hasMember['member_id'];
                        }
                    }
                    Db::name('MemberOauth')->where('openid',$user['openid'])
                        ->update($userData);
                }
            }
        }else{
            try{
                $result=$app->user->list($openid);
                $users = $app->user->select($result['data']['openid']);
                $openidChunk = array_chunk($result['data']['openid'],100);
                foreach($openidChunk as $openids){
                    $users = $app->user->select($openids);
                    $this->updateUsers($users['user_info_list'],$this->wid);
                }
            }catch(\Exception $e){
                $this->apiException($e);
            }

            $sesskey='fans_count_'.$wechat['appid'];
            $count=(int)session($sesskey);
            $count+=$result['count'];
            if($count<$result['total']) {
                session($sesskey,$count);
                $this->success('已同步：' . $count, '', ['next_openid' => $result['next_openid'],'count'=>$count,'total'=>$result['total']]);
            }else{
                session($sesskey,null);
            }
        }

        $this->success('同步成功');
    }
    private function updateUsers($userinfos,$wid){
        $openids=array_column($userinfos,'openid');
        $userauths=Db::name('MemberOauth')->whereIn('openid',$openids)->select();
        $userauths=array_index($userauths,'openid');
        foreach ($userinfos as $user){
            $userData=MemberOauthModel::mapUserInfo($user);
            $userData['type']=$this->currentWechat['account_type'];
            $userData['type_id']=$wid;
            if(isset($userauths[$user['openid']])) {
                if(!empty($user['unionid'])){
                    if(!$userauths[$user['openid']]['member_id']){
                        $hasMember = Db::name('MemberOauth')->where('unionid',$userData['unionid'])->where('member_id','>',0)->find();
                        if(!empty($hasMember['member_id'])){
                            $userData['member_id']=$hasMember['member_id'];
                        }
                    }
                }
                Db::name('MemberOauth')->where('openid', $user['openid'])
                        ->update($userData);
                
            }else{
                $userData['member_id']=0;
                if(!empty($user['unionid'])){
                    $hasMember = Db::name('MemberOauth')->where('unionid',$userData['unionid'])->where('member_id','>',0)->find();
                    if(!empty($hasMember['member_id'])){
                        $userData['member_id']=$hasMember['member_id'];
                    }
                }
                
                $userData['email']='';
                $userData['is_follow']=1;
                Db::name('MemberOauth')->insert($userData);
                
            }
        }
    }
}