<?php

namespace app\admin\controller\wechat;

use EasyWeChat\OfficialAccount\Application;

/**
 * 菜单管理
 * Class MenuController
 * @package app\admin\controller\wechat
 */
class MenuController extends WechatBaseController
{
    /**
     * 自定义菜单
     * todo 个性化菜单列表及编辑
     * @param int $refresh
     * @return mixed
     */
    public function edit($refresh=0)
    {
        if(!$this->wechatApp instanceof Application){
            $this->error('该类型账号不支持自定义菜单功能');
        }
        $app=$this->wechatApp;
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


}