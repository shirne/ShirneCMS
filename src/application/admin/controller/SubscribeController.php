<?php
namespace app\admin\controller;

use think\facade\Db;

class SubscribeController extends BaseController
{
    public function index($key="")
    {
        if($this->request->isPost()){
            return redirect(url('',['key'=>base64_encode($key)]));
        }
        $key=empty($key)?"":base64_decode($key);
        $model = Db::name('subscribe');
        
        if(!empty($key)){
            $model->whereLike('title|email',"%$key%");
        }
        $lists=$model->order('ID DESC')->paginate(15);
        $this->assign('lists',$lists);
        $this->assign('page',$lists->render());
        return $this->fetch();
    }
}