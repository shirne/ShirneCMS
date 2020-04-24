<?php


namespace app\admin\controller\shop;

use app\admin\controller\BaseController;
use think\Db;
use think\response\Redirect;

/**
 * 订单统计
 * Class OrderStaticsController
 * @package app\admin\controller\shop
 */
class OrderStaticsController extends BaseController
{
    /**
     * 订单统计
     * @param string $type
     * @param string $start_date
     * @param string $end_date
     * @return mixed
     */
    public function index($type='date',$start_date='',$end_date=''){
        if($this->request->isPost()){
            if(!in_array($type,['date','month','year']))$type='date';
            return redirect(url('',['type'=>$type,'start_date'=>$start_date,'end_date'=>$end_date]));
        }

        $format="'%Y-%m-%d'";

        if($type=='month'){
            $format="'%Y-%m'";
        }elseif($type=='year'){
            $format="'%Y'";
        }

        $model=Db::name('order')->field('count(order_id) as order_count,sum(payamount) as order_amount,sum(rebate_total) as order_rebate,sum(cost_amount) as total_cost_amount,date_format(from_unixtime(create_time),' . $format . ') as awdate');
        
        if(empty($start_date)){
            if($type=='date'){
                $start_date=date('Y-m-01',time());
            }elseif($type=='month'){
                $start_date=date('Y-01-01',time());
            }
        }
        
        $start_date=format_date($start_date,'Y-m-d');
        $end_date=format_date($end_date,'Y-m-d');
        if(!empty($start_date)){
            if(!empty($end_date)){
                $model->whereBetween('create_time',[strtotime($start_date),strtotime($end_date.' 23:59:59')]);
            }else{
                $model->where('create_time','>=',strtotime($start_date));
            }
        }else{
            if(!empty($end_date)){
                $model->where('create_time','<=',strtotime($end_date.' 23:59:59'));
            }
        }

        $statics=$model->where('status','>',0)->group('awdate')->select();

        $this->assign('statics',$statics);
        $this->assign('static_type',$type);
        $this->assign('start_date',$start_date);
        $this->assign('end_date',$end_date);
        return $this->fetch();
    }

    /**
     * 地区统计
     * @param string $type 
     * @param string $start_date 
     * @param string $end_date 
     * @return Redirect|string 
     */
    public function region($type='city',$start_date='',$end_date=''){
        if($this->request->isPost()){
            if(!in_array($type,['province','city']))$type='city';
            return redirect(url('',['type'=>$type,'start_date'=>$start_date,'end_date'=>$end_date]));
        }

        $model=Db::name('order')->field('count(order_id) as order_count,sum(payamount) as order_amount,sum(rebate_total) as order_rebate,sum(cost_amount) as total_cost_amount,`'.$type.'` as region');
        
        if(empty($start_date)){
            if($type=='date'){
                $start_date=date('Y-m-01',time());
            }elseif($type=='month'){
                $start_date=date('Y-01-01',time());
            }
        }
        
        $start_date=format_date($start_date,'Y-m-d');
        $end_date=format_date($end_date,'Y-m-d');
        if(!empty($start_date)){
            if(!empty($end_date)){
                $model->whereBetween('create_time',[strtotime($start_date),strtotime($end_date.' 23:59:59')]);
            }else{
                $model->where('create_time','>=',strtotime($start_date));
            }
        }else{
            if(!empty($end_date)){
                $model->where('create_time','<=',strtotime($end_date.' 23:59:59'));
            }
        }

        $statics=$model->where('status','>',0)->order('order_count DESC,order_amount DESC')->group('region')->select();

        $this->assign('statics',$statics);
        $this->assign('static_type',$type);
        $this->assign('start_date',$start_date);
        $this->assign('end_date',$end_date);
        return $this->fetch();
    }
}