<?php

namespace addon\credit_shop\admin\controller;

use addon\base\BaseController;

class PromotionController extends BaseController
{
    /**
     * 积分商城配置
     */
    public function index()
    {
        $setting = getSettings(true,true);
        if($this->request->isPost()){

            $data=$this->request->post();
            $result = save_setting($data,'credit');
            user_log($this->mid,'creditconfig',1,'修改积分商城配置' ,'manager');
            $this->success('配置已更新',url('credit_shop.promotion/index'));
        }
        $this->assign('setting',$setting['credit']);
        return $this->fetch();
    }


}