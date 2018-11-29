<?php

namespace app\admin\controller;

use app\common\model\WechatModel;
use app\common\model\WechatReplyModel;
use app\admin\validate\WechatReplyValidate;
use app\admin\validate\WechatValidate;
use app\common\model\MemberOauthModel;
use EasyWeChat\Factory;
use think\Db;

/**
 * 公众号管理
 * Class WechatController
 * @package app\admin\controller
 */
class WechatController extends BaseController
{
    /**
     * 公众号列表
     * @param string $key
     * @return mixed|\think\response\Redirect
     */
    public function index($key="")
    {
        if($this->request->isPost()){
            return redirect(url('',['key'=>base64_encode($key)]));
        }
        $key=empty($key)?"":base64_decode($key);
        $model = Db::name('wechat');
        $where=array();
        if(!empty($key)){
            $where[] = array('title|appid','like',"%$key%");
        }
        $lists=$model->where($where)->order('ID DESC')->paginate(15);
        $this->assign('lists',$lists);
        $this->assign('page',$lists->render());
        return $this->fetch();
    }

    /**
     * 添加公众号
     * @return mixed
     */
    public function add(){
        if ($this->request->isPost()) {
            //如果用户提交数据
            $data = $this->request->post();
            $validate=new WechatValidate();
            $validate->setId(0);

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                $uploaded = $this->upload('wechat', 'upload_logo');
                if (!empty($uploaded)) {
                    $data['logo'] = $uploaded['url'];
                }elseif($this->uploadErrorCode>102){
                    $this->error($this->uploadErrorCode.':'.$this->uploadError);
                }
                $uploaded = $this->upload('wechat', 'upload_qrcode');
                if (!empty($uploaded)) {
                    $data['qrcode'] = $uploaded['url'];
                }elseif($this->uploadErrorCode>102){
                    delete_image($data['logo']);
                    $this->error($this->uploadErrorCode.':'.$this->uploadError);
                }
                $model=WechatModel::create($data);
                if ($model['id']) {
                    $this->success(lang('Add success!'), url('wechat/index'));
                } else {
                    $this->error(lang('Add failed!'));
                }
            }
        }
        $model=array();
        $this->assign('model',$model);
        $this->assign('id',0);
        return $this->fetch('edit');
    }

    /**
     * 修改公众号
     * @param $id
     * @return mixed
     */
    public function edit($id)
    {
        if ($this->request->isPost()) {
            //如果用户提交数据
            $data = $this->request->post();
            $validate=new WechatValidate();
            $validate->setId($id);

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                $delete_images=[];
                $uploaded = $this->upload('wechat', 'upload_logo');
                if (!empty($uploaded)) {
                    $data['logo'] = $uploaded['url'];
                    $delete_images[]=$data['delete_logo'];
                }elseif($this->uploadErrorCode>102){
                    $this->error($this->uploadErrorCode.':'.$this->uploadError);
                }
                $uploaded = $this->upload('wechat', 'upload_qrcode');
                if (!empty($uploaded)) {
                    $data['qrcode'] = $uploaded['url'];
                    $delete_images[]=$data['delete_qrcode'];
                }elseif($this->uploadErrorCode>102){
                    delete_image($data['logo']);
                    $this->error($this->uploadErrorCode.':'.$this->uploadError);
                }
                $model=WechatModel::get($id);
                if ($model->allowField(true)->save($data)) {
                    delete_image($delete_images);
                    $this->success(lang('Update success!').$this->uploadError, url('wechat/index'));
                } else {
                    delete_image([$data['logo'],$data['qrcode']]);
                    $this->error(lang('Update failed!').$this->uploadError);
                }
            }
        }

        $model = Db::name('wechat')->find($id);
        if(empty($model)){
            $this->error('数据不存在');
        }
        $this->assign('model',$model);
        $this->assign('id',$id);
        return $this->fetch();
    }

    /**
     * 删除公众号
     * @param $id
     */
    public function delete($id)
    {
        $id = intval($id);
        $model = Db::name('wechat');
        $result = $model->delete($id);
        if($result){
            $this->success(lang('Delete success!'), url('wechat/index'));
        }else{
            $this->error(lang('Delete failed!'));
        }
    }

    private $currentWechat=null;

    private function get_app($wid){
        if(is_array($wid)){
            $wechat=$wid;
        }else {
            $wechat = Db::name('Wechat')->where('id', $wid)->find();
            if(empty($wechat)){
                $this->error('公众号信息不存在');
            }
        }
        $this->currentWechat=$wechat;

        return Factory::officialAccount([
            'token'=>$wechat['token'],
            'app_id'=>$wechat['appid'],
            'secret'=>$wechat['appsecret'],
            'aes_key'=>$wechat['encodingaeskey']
        ]);
    }

    /**
     * 自定义菜单
     * todo 个性化菜单列表及编辑
     * @param $id
     * @param int $refresh
     * @return mixed
     */
    public function menu($id,$refresh=0)
    {
        $app=$this->get_app($id);
        $model=$this->currentWechat;
        $cacheKey='wechat-menu-'.$model['appid'];

        if($this->request->isPost()){
            $data=$this->request->post('menu');
            $data=json_decode($data,true);
            foreach ($data as &$item) {
                if(!empty($item['sub_button'])){
                    foreach ($item as $k=>$val){
                        if(!in_array($k,['name','sub_button'])){
                            unset($item[$k]);
                        }
                    }
                }else{
                    unset($item['sub_button']);
                }
            }
            $result=$app->menu->create($data);
            if(!empty($result) && $result['errcode']=='0'){
                cache($cacheKey,$data);
                $this->success('保存成功');
            }else{
                $this->success('保存失败：'.$result['errmsg']);
            }
        }
        $menuData=cache($cacheKey);
        if(empty($menuData) || $refresh){
            $menuData=$app->menu->list();
            if(empty($menuData) || $menuData['errcode']!=0){
                $menuData=$app->menu->current();
                if(!empty($menuData) && empty($menuData['errcode']) && !empty($menuData['selfmenu_info']['button'])){
                    $menuData=$menuData['selfmenu_info']['button'];
                    foreach ($menuData as $k=>$item){
                        if(isset($item['sub_button'])){
                            $menuData[$k]['sub_button']=$item['sub_button']['list'];
                        }
                    }
                }else{
                    $menuData=[];
                }
            }else{
                $menuData=$menuData['menu']['button'];
            }

            if(empty($menuData))$menuData=[];
            cache($cacheKey,$menuData);
        }

        $this->assign('model',$model);
        $this->assign('menuData',$menuData);
        return $this->fetch();
    }

    public function fans($wid){
        $wechat=Db::name('Wechat')->where('id',$wid)->find();
        if(empty($wechat)){
            $this->error('公众号信息不存在');
        }
        $model=Db::name('MemberOauth')->where('type_id',$wid);

        $lists=$model->paginate(15);
        $this->assign('lists',$lists);
        $this->assign('page',$lists->render());
        $this->assign('wid',$wid);
        return $this->fetch();
    }

    /**
     * 同步粉丝资料
     * @param $wid
     * @param string $openid
     * @param bool $single
     */
    public function syncfans($wid,$openid='',$single=false){
        $app=$this->get_app($wid);
        $wechat=$this->currentWechat;

        if($single) {
            if(strpos($openid,',')===false) {
                $user = $app->user->get($openid);
                Db::name('MemberOauth')->where('openid',$openid)
                ->update(MemberOauthModel::mapUserInfo($user));
            }else {
                $users = $app->user->select(explode(',', $openid));
                foreach ($users as $user){
                    Db::name('MemberOauth')->where('openid',$user['openid'])
                        ->update(MemberOauthModel::mapUserInfo($user));
                }
            }
        }else{
            $result=$app->user->list($openid);
            $users = $app->user->select($result['data']['openid']);
            $this->updateUsers($users,$wid);

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
                Db::name('MemberOauth')->where('openid', $user['openid'])
                    ->update($userData);
            }
        }
    }

    /**
     * 素材管理
     * @param $wid
     * @param $key
     * @param string $type
     * @param int $page
     * @return mixed
     */
    public function material($wid,$key='',$type='news',$page=1){
        if($this->request->isPost()){
            return redirect(url('',['wid'=>$wid,'key'=>base64_encode($key),'type'=>$type]));
        }
        $key=empty($key)?"":base64_decode($key);
        $model = Db::name('wechatMaterial');
        $where=array();
        if(!empty($key)){
            $where[] = array('title|keyword','like',"%$key%");
        }
        $lists=$model->where($where)->order('update_time DESC')->paginate(15);
        $this->assign('lists',$lists);
        $this->assign('page',$lists->render());
        return $this->fetch();
    }

    /**
     * 素材同步
     * @param $wid
     * @param $type
     */
    public function materialsync($wid,$type){
        $app=$this->get_app($wid);

        $count=0;
        $totals=$app->material->stats();
        $total_count=$totals[$type.'_count'];
        while($count<$total_count){
            $materials=$app->material->list($type,$count,20);
            if(!empty($materials['item'])){
                foreach ($materials['item'] as $item){
                    $exist=Db::name('wechatMaterial')->where('media_id',$item['media_id'])->find();
                    $data=[
                        'type'=>$type,
                        'wechat_id'=>$wid,
                        'update_time'=>$item['update_time']
                    ];
                    if(in_array($type,['image','voice','video'])){
                        $data['url']=$item['url'];
                        $data['title']=$item['name'];
                    }
                    if($type=='news'){
                        $data['content']=json_encode($item['content'],JSON_UNESCAPED_UNICODE);
                    }
                    if(empty($exist)){
                        $data['media_id']=$item['media_id'];
                        Db::name('wechatMaterial')->insert($data);
                    }else{
                        Db::name('wechatMaterial')->where('id',$exist['id'])
                            ->update($data);
                    }
                }
            }

            $count += $materials['ITEM_COUNT'];
        }
        $this->success('同步成功');
    }

    /**
     * 素材删除
     * @param $media_id
     * @param $wid
     */
    public function materialdelete($media_id,$wid){
        $app=$this->get_app($wid);

        $app->material->delete($media_id);
        Db::name('wechatMaterial')->where('media_id',$media_id)->delete();

        $this->success('删除成功');
    }

    /**
     * 回复管理
     * @param $wid
     * @param $key
     * @return mixed
     */
    public function reply($wid,$key=''){
        if($this->request->isPost()){
            return redirect(url('wechat/reply',['wid'=>$wid,'key'=>base64_encode($key)]));
        }
        $model=Db::name('WechatReply')->where('wechat_id',$wid);
        if(!empty($key)){
            $key=base64_decode($key);
            $model->whereLike('title|keyword',"%$key%");
        }

        $lists=$model->paginate(15);
        $this->assign('keyword',$key);
        $this->assign('lists',$lists);
        $this->assign('types',getWechatTypes());
        $this->assign('reply_types',getWechatReplyTypes());
        $this->assign('page',$lists->render());
        $this->assign('wid',$wid);
        return $this->fetch();
    }

    /**
     * 新建回复
     * @param $wid
     * @return mixed
     */
    public function replyadd($wid){
        $wechat=Db::name('Wechat')->where('id',$wid)->find();
        if(empty($wechat)){
            $this->error('公众号信息不存在');
        }
        if($this->request->isPost()){
            $data=$this->request->post();
            $validate=new WechatReplyValidate();
            $validate->setId(0);
            if(!$validate->check($data)){
                $this->error($validate->getError());
            }else{
                switch($data['reply_type']){
                    case 'text':
                        break;
                    case 'news':
                        $data['news']=empty($data['news'])?[]:array_values($data['news']);
                        $data['content']=json_encode($data['news']);
                        break;
                    case 'image':
                        $uploaded = $this->upload('wechat', 'upload_image');
                        if (!empty($uploaded)) {
                            $data['content'] = json_encode(['image'=>$uploaded['url']]);
                        }elseif($this->uploadErrorCode>102){
                            $this->error($this->uploadErrorCode.':'.$this->uploadError);
                        }
                        break;
                    case 'custom':
                        $data['content']=json_encode($data['custom']);
                        break;
                    default:
                        $this->error('回复类型错误');
                        break;
                }

                unset($data['news']);
                unset($data['custom']);

                $added=WechatReplyModel::create($data);
                if($added){
                    $this->success('添加成功');
                }else{
                    $this->error('添加失败');
                }
            }
        }

        $model=[
            'wechat_id'=>$wid,
            'sort'=>1,
            'type'=>'keyword',
            'reply_type'=>'text',
            'news'=>[],
            'module'=>[]
        ];
        $this->assign('wechat',$wechat);
        $this->assign('model',$model);
        $this->assign('types',getWechatTypes());
        $this->assign('reply_types',getWechatReplyTypes());
        return $this->fetch('replyedit');
    }

    /**
     * 编辑回复
     * @param $id
     * @param int $wid
     * @return mixed
     */
    public function replyedit($id,$wid=0){
        $model=Db::name('WechatReply')->where('id',$id)->find();
        if(empty($model)){
            $this->error('数据不存在');
        }
        if($this->request->isPost()){
            $data=$this->request->post();
            $validate=new WechatReplyValidate();
            $validate->setId($id);
            if(!$validate->check($data)){
                $this->error($validate->getError());
            }else{
                $delete_images=[$data['delete_image']];
                switch($data['reply_type']){
                    case 'text':
                        break;
                    case 'news':
                        $data['news']=empty($data['news'])?[]:array_values($data['news']);
                        $data['content']=json_encode($data['news']);
                        break;
                    case 'image':
                        $uploaded = $this->upload('wechat', 'upload_image');
                        if (!empty($uploaded)) {
                            $data['content'] = json_encode(['image'=>$uploaded['url']]);
                        }elseif($this->uploadErrorCode>102){
                            $this->error($this->uploadErrorCode.':'.$this->uploadError);
                        }else{
                            unset($data['content']);
                            unset($delete_images[0]);
                        }
                        break;
                    case 'custom':
                        $data['content']=json_encode($data['custom']);
                        break;
                    default:
                        $this->error('回复类型错误');
                        break;
                }

                unset($data['news']);
                unset($data['custom']);


                $added=WechatReplyModel::update($data,['id'=>$id]);
                if($added){
                    delete_image($delete_images);
                    $this->success('保存成功');
                }else{
                    $this->error('保存失败');
                }
            }
        }
        if($model['reply_type']=='news'){
            $model['news']=$this->decodeContent($model['content']);
            $model['content']='';
        }elseif($model['reply_type']=='image'){
            $model['data']=$this->decodeContent($model['content']);
            $model['content']='';
        }elseif($model['reply_type']=='custom'){
            $model['module']=$this->decodeContent($model['content']);
            $model['content']='';
        }

        $wechat=Db::name('Wechat')->where('id',$model['wechat_id'])->find();
        $this->assign('wechat',$wechat);
        $this->assign('model',$model);
        $this->assign('types',getWechatTypes());
        $this->assign('reply_types',getWechatReplyTypes());
        return $this->fetch();
    }

    private function decodeContent($content){
        $result=json_decode($content,TRUE);
        return empty($result)?[]:$result;
    }

    /**
     * 删除回复
     * @param $id
     * @param $wid
     */
    public function replydelete($id,$wid)
    {
        $model = Db::name('wechatReply');
        $result = $model->where('id','in',idArr($id))->delete();
        if($result){
            user_log($this->mid,'deletewechatreply',1,'删除回复消息 '.$id ,'manager');
            $this->success("删除成功", url('wechat/reply',['wid'=>$wid]));
        }else{
            $this->error("删除失败");
        }
    }
}