<?php

namespace app\admin\controller\shop;

use app\admin\controller\BaseController;

class PromotionController extends BaseController
{
    /**
     * 配置
     */
    public function index()
    {
        $setting = getSettings(true,true);
        if($this->request->isPost()){

            $data=$this->request->post();
            $result = save_setting($data,'shop');
            user_log($this->mid,'shopconfig',1,'修改商城配置' ,'manager');
            $this->success('配置已更新',url('shop.promotion/index'));
        }
        $this->assign('setting',$setting['shop']);
        return $this->fetch();
    }

    public function message()
    {
        $setting = getSettings(true,true);
        if($this->request->isPost()){

            $data=$this->request->post();
            $result = save_setting($data,'message');
            user_log($this->mid,'messageconfig',1,'修改消息配置' ,'manager');
            $this->success('配置已更新',url('shop.promotion/message'));
        }
        $this->assign('setting',$setting['message']);
        return $this->fetch();
    }

    public function poster()
    {
        $setting = getSettings(true,true);
        if($this->request->isPost()){

            $data=$this->request->post();
            $files = $this->_batchUpload('poster',['poster_background','poster_qrlogo']);
            if(!empty($files)){
                foreach($files as $k=>$file){
                    $data['v-'.$k]=$file;
                }
            }
            $result = save_setting($data,'poster');
            if(!empty($this->deleteFiles)){
                delete_image($this->deleteFiles);
            }
            user_log($this->mid,'posterconfig',1,'修改推广海报配置' ,'manager');
            $this->success('配置已更新',url('shop.promotion/poster'));
        }
        $this->assign('setting',$setting['poster']);
        return $this->fetch();
    }

    
    public function share()
    {
        $setting = getSettings(true,true);
        if($this->request->isPost()){

            $data=$this->request->post();
            $files = $this->_batchUpload('share',['share_background','share_qrlogo']);
            if(!empty($files)){
                foreach($files as $k=>$file){
                    $data['v-'.$k]=$file;
                }
            }
            $result = save_setting($data,'share');
            if(!empty($this->deleteFiles)){
                delete_image($this->deleteFiles);
            }
            user_log($this->mid,'shareconfig',1,'修改产品分享海报配置' ,'manager');
            $this->success('配置已更新',url('shop.promotion/share'));
        }
        $this->assign('setting',$setting['share']);
        return $this->fetch();
    }

}