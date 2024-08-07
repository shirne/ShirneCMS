<?php

namespace app\admin\controller\credit;

use app\admin\controller\BaseController;

class PromotionController extends BaseController
{
    /**
     * 积分商城配置
     */
    public function index()
    {
        $setting = getSettings(true, true);
        if ($this->request->isPost()) {

            $data = $this->request->post();
            $result = save_setting($data, 'credit');
            user_log($this->mid, 'credit-config', 1, '修改积分商城配置', 'manager');
            $this->success('配置已更新', url('credit.promotion/index'));
        }
        $this->assign('setting', $setting['credit']);
        return $this->fetch();
    }
}
