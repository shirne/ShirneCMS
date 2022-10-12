<?php

namespace app\admin\controller\wechat;
use app\admin\validate\WechatReplyValidate;
use app\common\model\WechatReplyModel;
use think\Db;

/**
 * 回复管理
 * Class ReplyController
 * @package app\admin\controller\wechat
 */
class ReplyController extends WechatBaseController
{
    /**
     * 回复管理
     * @param $wid
     * @param $key
     * @return mixed
     */
    public function index($key=''){
        if($this->request->isPost()){
            return redirect(url('wechat/reply',['wid'=>$this->wid,'key'=>base64url_encode($key)]));
        }
        $model=Db::name('WechatReply')->where('wechat_id',$this->wid);
        if(!empty($key)){
            $key=base64url_decode($key);
            $model->whereLike('title|keyword',"%$key%");
        }

        $lists=$model->paginate(15);
        $this->assign('keyword',$key);
        $this->assign('lists',$lists);
        $this->assign('types',getWechatTypes());
        $this->assign('reply_types',getWechatReplyTypes());
        $this->assign('page',$lists->render());
        return $this->fetch();
    }

    /**
     * 新建回复
     * @return mixed
     */
    public function add(){
        $wechat=Db::name('Wechat')->where('id',$this->wid)->find();
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
                        $data['content']=json_encode($data['news'],JSON_UNESCAPED_UNICODE);
                        break;
                    case 'image':
                        $uploaded = $this->_upload('wechat', 'upload_image');
                        if (!empty($uploaded)) {
                            $data['content'] = json_encode(['image'=>$uploaded['url']]);
                        }elseif($this->uploadErrorCode>102){
                            $this->error($this->uploadErrorCode.':'.$this->uploadError);
                        }
                        break;
                    case 'custom':
                        $data['content']=json_encode($data['custom'],JSON_UNESCAPED_UNICODE);
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
            'wechat_id'=>$this->wid,
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
        return $this->fetch('edit');
    }

    /**
     * 编辑回复
     * @param $id
     * @param int $wid
     * @return mixed
     */
    public function edit($id,$wid=0){
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
                        $data['content']=json_encode($data['news'],JSON_UNESCAPED_UNICODE);
                        break;
                    case 'image':
                        $uploaded = $this->_upload('wechat', 'upload_image');
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
                        $data['content']=json_encode($data['custom'],JSON_UNESCAPED_UNICODE);
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
    public function delete($id,$wid)
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