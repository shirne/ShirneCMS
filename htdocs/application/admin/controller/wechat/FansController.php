<?php

namespace app\admin\controller\wechat;

use app\common\model\MemberOauthModel;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Text;
use think\Db;

/**
 * 粉丝管理
 * Class FansController
 * @package app\admin\controller\wechat
 */
class FansController extends WechatBaseController
{
    public function index(){
        $model=Db::name('MemberOauth')->where('type_id',$this->wid);

        $lists=$model->paginate(15);
        $this->assign('lists',$lists);
        $this->assign('page',$lists->render());
        return $this->fetch();
    }
    
    public function sendmsg($openid, $msgtype, $content){
        $service = $this->wechatApp->customer_service;
        if($msgtype == 'text'){
            $messager = $service->message(new Text($content));
        }elseif($msgtype=='news'){
            $content['image']=local_media($content['image']);
            $messager = $service->message(new News([new NewsItem($content)]));
        }else{
            $this->error('暂时不支持的消息');
        }
    
        $result=$messager->to($openid)->send();
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
                $user = $app->user->get($openid);
                Db::name('MemberOauth')->where('openid',$openid)
                    ->update(MemberOauthModel::mapUserInfo($user));
            }else {
                $users = $app->user->select(explode(',', $openid));
                foreach ($users['user_info_list'] as $user){
                    Db::name('MemberOauth')->where('openid',$user['openid'])
                        ->update(MemberOauthModel::mapUserInfo($user));
                }
            }
        }else{
            $result=$app->user->list($openid);
            $users = $app->user->select($result['data']['openid']);
            $this->updateUsers($users['user_info_list'],$this->wid);

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
            if(isset($userauths[$user['openid']])) {
                Db::name('MemberOauth')->where('openid', $user['openid'])
                    ->update($userData);
            }else{
                $userData['email']='';
                $userData['is_follow']=1;
                $userData['member_id']=0;
                $userData['type']='wechat';
                $userData['type_id']=$wid;
                Db::name('MemberOauth')->insert($userData);
            }
        }
    }
}