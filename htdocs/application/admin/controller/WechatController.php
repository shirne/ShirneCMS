<?php

namespace app\admin\controller;

use app\admin\model\WechatModel;
use app\admin\validate\WechatValidate;
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
                    $this->success("添加成功", url('wechat/index'));
                } else {
                    $this->error("添加失败");
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
                    $this->success("更新成功".$this->uploadError, url('wechat/index'));
                } else {
                    delete_image([$data['logo'],$data['qrcode']]);
                    $this->error("更新失败".$this->uploadError);
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
            $this->success("删除成功", url('wechat/index'));
        }else{
            $this->error("删除失败");
        }
    }

    /**
     * 自定义菜单
     * @param $id
     * @param int $refresh
     * @return mixed
     */
    public function menu($id,$refresh=0)
    {
        $model = Db::name('wechat')->find($id);
        if(empty($model)){
            $this->error('公众号不存在');
        }
        $cacheKey='wechat-menu-'.$model['appid'];

        $app=Factory::officialAccount([
            'token'=>$model['token'],
            'aes_key'=>$model['encodingaeskey'],
            'app_id'=>$model['appid'],
            'secret'=>$model['appsecret']
        ]);

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
}