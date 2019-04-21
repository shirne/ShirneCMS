<?php
namespace app\index\controller;

use app\common\model\AdvGroupModel;
use app\common\model\ArticleModel;
use think\Db;

class IndexController extends BaseController
{
    public function index()
    {
        $this->seo();
        return $this->fetch();
    }

    public function share(){
        $product_id=getSetting('share_product');

        //判断会员是否已购买
        if($this->isLogin) {
            $hasBuy = Db::view('orderProduct', '*')
                ->view('order', '*', 'order.order_id=orderProduct.order_id', 'LEFT')
                ->where('orderProduct.product_id', $product_id)
                ->where('order.member_id', $this->userid)
                ->find();
            if(!empty($hasBuy)){
                return redirect(url('index/product/index'));
            }
        }

        return redirect(url('index/product/view',['id'=>$product_id]));

    }
}
