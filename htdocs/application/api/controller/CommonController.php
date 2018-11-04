<?php

namespace app\api\Controller;
use app\common\model\AdvGroupModel;

/**
 * 通用接口
 * Class CommonController
 * @package app\api\Controller
 */
class CommonController extends BaseController
{
    /**
     * 获取广告图
     * @param $flag
     */
    public function advs($flag){
        return $this->response(AdvGroupModel::getAdList($flag));
    }
}