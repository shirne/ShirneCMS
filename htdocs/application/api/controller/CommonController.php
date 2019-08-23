<?php

namespace app\api\Controller;

use app\common\model\AdvGroupModel;
use app\common\model\LinksModel;
use app\common\model\MemberSignModel;
use app\common\model\NoticeModel;
use think\Db;
use think\Log;
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
     * 两种请求方式
     * 1. methods = a,b,c.d ; arg1 = a ;  arg2 = b ; ...
     *    该方式不能重复调同一个接口，各接口间参数不能有冲突
     * 2. { method1 => { arg1 => a, arg2 => b}, method2 => { call => controller.method, arg1 => a, arg2 => b} }
     *    该方式可以重复调用同一个接口，key指定不同，增加一个call来指定调用，各调用的参数互相隔离
     * @return \think\response\Json
     */
    public function batch(){
        $methods=$this->get_param('methods');
        $data=[];
        if(!empty($methods)) {
            $methods = explode(',', $methods);
            $params = $this->request->param();
            foreach ($methods as $method) {
                $data[$method] = $this->call_api($method, $params);
            }
        }else{

            foreach ($this->input as $method=>$arguments) {
                if($method == 'token')continue;
                
                if(isset($arguments['call']))$calls=$arguments['call'];
                else $calls=$method;

                $data[$method] = $this->call_api($calls, $arguments);
            }
        }
        return $this->response($data);
    }

    private function call_api($calls, $arguments = [])
    {
        $m = explode('.', $calls);
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
            try {
                $args = [];
                $reflect = new \ReflectionMethod($controller, $m);
                foreach ($reflect->getParameters() as $param) {
                    if (isset($arguments[$param->name])) {
                        $args[] = $arguments[$param->name];
                    } elseif ($this->request->has($param->name, 'get')) {
                        $args[] = $this->request->get($param->name);
                    } else {
                        $args[] = $param->getDefaultValue();
                    }
                }
                //call_user_func_array([$controller, $m],$args);
                $response = $reflect->invokeArgs($controller, $args);
                if($response instanceof Response) {
                    $curData = $response->getData();
    
                    return $curData['data'];
                }
            }catch (\ReflectionException $e){
                Log::record($e->getMessage(),'error');
            }
        }
        return null;
    }

    /**
     * 获取广告图
     * @param $flag
     * @return Response
     */
    public function advs($flag){
        return $this->response(AdvGroupModel::getAdList($flag));
    }
    
    public function notice($flag='', $id=0){
        $model = NoticeModel::where('status',1);
        if(!empty($flag)){
            $model->where('page',$flag);
        }
        if($id > 0){
            $model->where('id',$id);
        }
        
        return $this->response($model->order('create_time DESC')->find());
    }
    public function notices($flag='',$count=10){
        $model = NoticeModel::where('status',1);
        if(!empty($flag)){
            $model->where('page',$flag);
        }
        
        return $this->response($model->order('create_time DESC')->limit($count)->select());
    }
    
    public function links($group='',$islogo=-1,$count=10){
        $model = LinksModel::where('status',1);
        if(!empty($group)){
            $model->where('group',$group);
        }
        if($islogo > -1){
            if($islogo){
                $model->where('logo', '<>', '');
            }else {
                $model->where('logo', '');
            }
        }
        
        return $this->response($model->order('sort ASC,create_time DESC')->limit($count)->select());
    }
    
    //todo
    public function feedback(){
    
    }
    
    public function feedbacks($pagesize=10){
        $model = Db::view('feedback','*')
            ->view("member",["username","nickname","avatar"],"Feedback.member_id=member.id","LEFT")
            ->view("manager",["realname"=>"manager_name"],"Feedback.manager_id=manager.id","LEFT")
            ->where("Feedback.status",1)
            ->order("Feedback.create_time DESC");
        
        $list = $model->paginate($pagesize);
        
        return $this->response([
            'list'=>$list,
            'total'=>$list->total(),
            'page'=>$list->currentPage()
        ]);
    }
    
    //todo
    public function do_feedback(){
        $this->check_submit_rate();
        
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
    
    public function signrank($date=0){
        if(!$date)$time = time();
        else $time = strtotime($date);
        $ranking=MemberSignModel::getInstance()->getSignRank($time);
        return $this->response($ranking);
    }
}