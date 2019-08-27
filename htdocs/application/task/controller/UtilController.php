<?php

namespace app\task\controller;


use app\common\command\Install;
use app\common\model\ArticleModel;
use app\common\model\MemberRechargeModel;
use app\common\model\OrderModel;
use app\common\model\PayOrderModel;
use app\common\model\ProductModel;
use think\Console;
use think\console\Input;
use think\console\Output;
use think\Controller;
use think\Db;
use think\Response;

class UtilController extends Controller
{
    public function cropimage($img){
        return crop_image($img,$_GET);
    }

    public function cacheimage($img){
        $paths=explode('.',$img);
        if(count($paths)==3) {
            preg_match_all('/(w|h|q|m)(\d+)(?:_|$)/', $paths[1], $matches);
            $args = [];
            foreach ($matches[1] as $idx=>$key){
                $args[$key]=$matches[2][$idx];
            }
            $response = crop_image($paths[0].'.'.$paths[2], $args);
            if($response->getCode()==200) {
                file_put_contents(DOC_ROOT . '/' . $img, $response->getData());
            }
            return $response;
        }else{
            return redirect(ltrim(config('upload.default_img'),'.'));
        }
    }

    public function daily()
    {
        $time = time();
        $isbreaked=false;
        $orders = Db::name('Order')->where('status',0)
            ->where('noticed',0)
            ->where('create_time','<',time()-10*60)->select();
        $ids = [];
        foreach ($orders as $order){
            $ids[]=$order['order_id'];
            OrderModel::sendOrderMessage($order,'order_need_pay');
            if(time()-$time>25){
                $isbreaked=true;
                break;
            }
        }
        Db::name('Order')->whereIn('order_id',$ids)->update(['noticed'=>1]);
        if($isbreaked)exit;
    
        $orders = Db::name('Order')->where('status',0)
            ->where('create_time','<',time()-30*60)->select();
        foreach ($orders as $order){
            OrderModel::getInstance()->updateStatus(['status'=>-1,'cancel_time'=>time(),'reason'=>'订单长时间未支付自动取消'],['order_id'=>$order['order_id']]);
            if(time()-$time>25){
                break;
            }
        }
        exit;
    }

    public function install($sql='',$mode='')
    {
        $console=Console::init(false);
        $output=new Output('buffer');
        $args=['install'];
        if(!empty($sql)){
            $args[]='--sql';
            $args[]=$sql;
        }
        if(!empty($mode)){
            $args[]='--mode';
            $args[]=$mode;
        }
        if($this->request->has('admin','post')){
            $args[]='--admin';
            $args[]=$this->request->post('admin');
        }
        if($this->request->has('password','post')){
            $args[]='--password';
            $args[]=$this->request->post('password');
        }
        $input=new Input($args);

        $console->doRun($input, $output);
        return $output->fetch();
    }

}