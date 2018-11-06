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
                if (count($m) > 1) {
                    if(strtolower($m[0])=='common'){
                        $controller = $this;
                    }else {
                        $controller = \container()->make('\\app\\api\\controller\\' . ucfirst($m[0]) . 'Controller');
                    }
                    $m = $m[1];
                } else {
                    $controller = $this;
                    $m = $m[0];
                }

                if (method_exists($controller, $m)) {
                    $args=[];
                    $reflect=new \ReflectionMethod( $controller, $m);
                    foreach ($reflect->getParameters() as $param){
                        if($this->has_param($param->name)){
                            $args[]=$this->get_param($param->name);
                        }else{
                            break;
                        }
                    }
                    $response = call_user_func_array([$controller, $m],$args);
                    $curData = $response->getData();
                    $data[$method] =$curData['data'];
                }
            }
        }else{

            foreach ($this->input as $method=>$arguments) {
                $m = explode('.', $method);
                if (count($m) > 1) {
                    if(strtolower($m[0])=='common'){
                        $controller = $this;
                    }else {
                        $controller = \container()->make('\\app\\api\\controller\\' . ucfirst($m[0]) . 'Controller');
                    }
                    $m = $m[1];
                } else {
                    $controller = $this;
                    $m = $m[0];
                }

                if (method_exists($controller, $m)) {
                    $args=[];
                    $reflect=new \ReflectionMethod( $controller, $m);
                    foreach ($reflect->getParameters() as $param){
                        if(isset($arguments[$param->name])){
                            $args[]=$arguments[$param->name];
                        }elseif($this->request->has($param->name,'get')){
                            $args[]=$this->request->get($param->name);
                        }else{
                            break;
                        }
                    }
                    $response = call_user_func_array([$controller, $m],$args);
                    $curData = $response->getData();
                    $data[$method] =$curData['data'];
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
        $data=[];
        foreach ($settings['common'] as $k=>$v){
            if(strpos($k,'site-')===0){
                $data[substr($k,5)]=$v;
            }else{
                $data[$k]=$v;
            }
        }
        return $this->response($data);
    }
}