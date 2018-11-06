<?php

namespace app\api\Controller;
use app\common\model\AdvGroupModel;
use think\Container;
use think\Response;

/**
 * 通用接口
 * Class CommonController
 * @package app\api\Controller
 */
class CommonController extends BaseController
{
    /**
     * 批量获取接口数据
     * @return \think\response\Json
     */
    public function batch(){
        $methods=$this->get_param('methods');
        $data=[];
        if(!empty($methods)) {
            $methods = explode(',', $methods);

            foreach ($methods as $method) {
                $m = explode('.', $method);
                if (count($m) == 2) {
                    $controller = $this;
                    $m = $m[0];
                } else {
                    if(strtolower($m[0])=='common'){
                        $controller = $this;
                    }else {
                        $controller = \container()->make('\\app\\api\\controller\\' . ucfirst($m[0]) . 'Controller');
                    }
                    $m = $m[1];
                }

                if (method_exists($controller, $m)) {
                    $response = call_user_func([$controller, $m]);
                    $data[$method] = $response->data();
                }
            }
        }
        return $this->response($data);
    }

    /**
     * 获取广告图
     * @param $flag
     * @return Response
     */
    public function advs($flag){
        return $this->response(AdvGroupModel::getAdList($flag));
    }

    public function siteinfo(){
        $settings=getSettings(false,true);
        return $this->response($settings['common']);
    }
}