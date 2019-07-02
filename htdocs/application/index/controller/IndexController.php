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

    public function subscribe()
    {
        if($this->request->isPost()){
            $email = $this->request->post('email');
            $pattern = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/";
            if(!preg_match($pattern, $mail, $matches)){
                $this->error('请填写正确的邮箱格式');
            }
            list($title, $domain) = explode('@',$email);
            $exists = Db::name('subscribe')->where('email',$email)->find();
            if(empty($exists)){
                Db::name('subscribe')->insert([
                    'title'=>$title,
                    'email'=>$email,
                    'is_subscribe'=>1,
                    'create_time'=>time(),
                    'update_time'=>time()
                ]);
            }elseif($exists['is_subscribe']==0){
                Db::name('subscribe')->where('email',$email)->update(['is_subscribe'=>1,'update_time'=>time()]);
            }
            $this->success('感谢您的订阅！');
        }
        $this->error('参数错误');
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
