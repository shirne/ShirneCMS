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
    public function poster()
    {
        $setting = getSettings(true,true);
        if($this->request->isPost()){

            $data=$this->request->post();
            $files = $this->batchUpload('poster',['poster_background','poster_qrlogo']);
            if(!empty($files)){
                foreach($files as $k=>$file){
                    $data['v-'.$k]=$file;
                }
            }
            $result = save_setting($data,'poster');
            if(!empty($this->deleteFiles)){
                delete_image($this->deleteFiles);
            }
            user_log($this->mid,'posterconfig',1,'修改分享图配置' ,'manager');
            $this->success('配置已更新',url('shop.promotion/poster'));
        }
        $this->assign('setting',$setting['poster']);
        return $this->fetch();
    }

}