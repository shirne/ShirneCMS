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

    private function createHash($id=0){
        $hash=random_str(rand(6,10));
        $exists=Db::name('wechat')->where('hash',$hash)->where('id','NEQ',$id)->find();
        if(!empty($exists)){
            return $this->createHash($id);
        }
        return $hash;
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
                $uploads=$this->batchUpload('wechat',['logo','qrcode','cert_path','key_path']);
                if($uploads){
                    $data=array_merge($data,$uploads);
                }
                $data['hash']=$this->createHash();
                if(!isset($data['is_default'])){
                    $data['is_default']=0;
                }
                if($data['account_type']=='service') {
                    $default = Db::name('wechat')->where('type', $data['type'])
                        ->where('is_default', 1)->find();
                    if(empty($default)){
                        $data['is_default']=1;
                    }
                }else{
                    $data['is_default']=0;
                }
                $model=WechatModel::create($data);
                if ($model['id']) {
                    if($data['is_default']){
                        Db::name('wechat')->where('type', $data['type'])
                            ->where('is_default', 1)
                        ->where('id','NEQ',$model['id'])->update(['is_default'=>0]);
                    }
                    $this->success(lang('Add success!'), url('wechat/index'));
                } else {
                    delete_image($uploads);
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
        $id=intval($id);
        if($id==0)$this->error('数据不存在');
        if ($this->request->isPost()) {
            //如果用户提交数据
            $data = $this->request->post();
            $validate=new WechatValidate();
            $validate->setId($id);

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                $uploads=$this->batchUpload('wechat',['logo','qrcode','cert_path','key_path']);
                if($uploads){
                    $data=array_merge($data,$uploads);
                }
                if(!isset($data['is_default'])){
                    $data['is_default']=0;
                }
                if($data['account_type']=='service') {
                    $default = Db::name('wechat')->where('type', $data['type'])
                        ->where('is_default', 1)->find();
                    if(empty($default)){
                        $data['is_default']=1;
                    }
                }/*else{
                    $data['is_default']=0;
                }*/
                $model=WechatModel::get($id);
                if(empty($model['hash'])){
                    $data['hash']=$this->createHash();
                }
                if ($model->allowField(true)->save($data)) {
                    delete_image($this->deleteFiles);
                    if($data['is_default']){
                        Db::name('wechat')->where('type', $data['type'])
                            ->where('is_default', 1)
                            ->where('id','NEQ',$id)->update(['is_default'=>0]);
                    }
                    $this->success(lang('Update success!').$this->uploadError, url('wechat/index'));
                } else {
                    delete_image($uploads);
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
     * 上传域名验证文件
     * @param $name
     * @param $content
     */
    public function uploadVerify($name,$content){
        if(is_writable(DOC_ROOT)){
            if(preg_match('/^MP_verify_[a-zA-Z0-9]+\.txt$/',$name)) {
                if(preg_match('/^[a-zA-Z0-9=\\/]+$/',$content)) {
                    file_put_contents(DOC_ROOT . '/' . $name, $content);
                    $this->success('上传成功！');
                }
            }
            $this->error('非法格式');
        }
        $this->error('网站目录无写入权限，请手动上传');
    }

    /**
     * 更新指定字段
     * @param $id
     * @param $field
     * @param $value
     */
    public function updateField($id,$field,$value){
        $id=intval($id);
        if($id==0)$this->error('数据不存在');
        $model = Db::name('wechat')->find($id);
        if(empty($model)){
            $this->error('数据不存在');
        }
        if(!in_array($field,['hash','token','encodingaeskey'])){
            $this->error('不允许更新的字段');
        }
        Db::name('wechat')->where('id',$id)->update([$field=>$value]);
        $this->success('更新成功');
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

}