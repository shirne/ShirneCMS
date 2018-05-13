<?php
/**
 * 订单统计
 * User: shirne
 * Date: 2018/5/11
 * Time: 18:17
 */

namespace app\admin\controller;


use think\Db;

class OrderStaticsController extends BaseController
{
    public function index(){
        $format="'%Y-%m-%d'";

        $statics=Db::name('order')->field('count(order_id) as order_count,date_format(from_unixtime(create_time),' . $format . ') as awdate')->group('awdate')->select();

        $this->assign('statics',$statics);
        return $this->fetch();
    }
}