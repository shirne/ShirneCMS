<?php

namespace app\api\controller;

use app\common\model\AdvGroupModel;
use app\common\model\BoothModel;
use app\common\model\LinksModel;
use app\common\model\MemberAgentModel;
use app\common\model\MemberSignModel;
use app\common\model\NoticeModel;
use think\Db;
use think\facade\Log;
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
        $methods=$this->request->param('methods');
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

                if(!is_array($arguments)){
                    if(empty($arguments))$arguments=[];
                    else continue;
                } 
                
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
                if(strpos($m[0],'/')>0 || strpos($m[0],'\\')>0){
                    $m[0] = str_replace('\\','/',$m[0]);
                    $layers = explode('/',strtolower($m[0]));
                    $last = count($layers)-1;
                    $layers[$last] = ucfirst($layers[$last]);
                    $m[0] = implode('\\',$layers);
                }else{
                    $m[0] = ucfirst($m[0]);
                }
                if(in_array($m[0],['Base','Auth','Authed','Wechat'])){
                    return null;
                }
                try{
                    $controller = \container()->make('\\app\\api\\controller\\' . $m[0] . 'Controller');
                }catch(\Exception $e){
                    Log::record($e->getMessage(),'error');
                    return null;
                }
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
                }else{
                    echo var_dump($response);
                    exit;
                }
            }catch (\ReflectionException $e){
                Log::record($e->getMessage(),'error');
            }catch (\Exception $e){
                Log::record($e->getMessage(),'error');
            }
        }
        return null;
    }
    
    public function booth($flags){
        return $this->response(BoothModel::fetchBooth($flags));
    }

    /**
     * 全站搜索
     */
    public function search($keyword, $model='', $price=''){

        $unionModel = Db::field('id,title,vice_title,cover as image,0 as price,status,0 sale,create_time,update_time,\'article\' as model')
        ->name('article');
        if(empty($model) || $model=='product'){
            $unionModel->union(function($query){
                $query->field('id,title,vice_title,image,min_price as price,status,sale,create_time,update_time,\'product\' as model')
                ->name('product');
            });
        }
        if(empty($model) || $model=='goods'){
            $unionModel->union(function($query){
                $query->field('id,title,vice_title,image,price,status,sale,create_time,update_time,\'goods\' as model')
                ->name('goods');
            });
        }

        $table=Db::table(
            $unionModel->buildSql().' search_table')
        ->where('status',1);

        if(!empty($keyword)){
            $table->whereLike('title|vice_title',"%$keyword%");
        }
        if(!empty($price)){
            $parts = explode('-',$price);
            if(count($parts)>1){
                $table->whereBetween('price',[intval($parts[0]),intval($parts[1])]);
            }else{
                $table->where('price','gt',intval($price));
            }
        }
        if(!empty($model)){
            $table->where('model',$model);
        }

        $lists = $table->order('sale DESC,create_time DESC')->paginate(10);

        return $this->response([
            'lists'=>$lists->items(),
            'page'=>$lists->currentPage(),
            'total'=>$lists->total(),
            'total_page'=>$lists->lastPage(),
        ]);
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

    /**
     * 获取配置
     */
    public function config($group){
        if(!empty($group) && $group != 'third'){
            $settings = getSettings(false,true);
            if(strpos($group,',')===false){
                return $this->response(isset($settings[$group])?$settings[$group]:new \stdClass());
            }else{
                $groups = explode(',',$group);
                $rdata=[];
                foreach($groups as $g){
                    $g = trim(strtolower($g));
                    if($g != 'third'){
                        $rdata[$g] = isset($settings[$group])?$settings[$group]:new \stdClass();
                    }
                }

                return $this->response($rdata);
            }
        }
        return $this->response(new \stdClass());
    }
    
    /**
     * 签到排名
     * @param int $date
     * @return \think\response\Json
     */
    public function signrank($date=0){
        if(!$date)$time = time();
        else $time = strtotime($date);
        $ranking=MemberSignModel::getInstance()->getSignRank($time);
        return $this->response($ranking);
    }
    
    /**
     * 公共数据
     */
    public function data($keys){
        $datas=[];
        $keyarr=explode(',',$keys);
        if(in_array('banklist',$keyarr)){
            $datas['banklist']=banklist();
        }
        if(in_array('log_types',$keyarr)){
            $datas['log_types']=getLogTypes();
        }
        if(in_array('money_fields',$keyarr)){
            $datas['money_fields']=getMoneyFields();
        }
        if(in_array('levels',$keyarr)){
            $datas['levels']=getMemberLevels();
        }
        if(in_array('agents',$keyarr)){
            $datas['agents']=MemberAgentModel::getCacheData();
        }
        
        return $this->response($datas);
    }
}