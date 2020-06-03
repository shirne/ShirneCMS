<?php
namespace app\admin\controller;

use think\Db;

class SubscribeController extends BaseController
{
    public function index($key="")
    {
        if($this->request->isPost()){
            return redirect(url('',['key'=>base64url_encode($key)]));
        }
        $key=empty($key)?"":base64url_decode($key);
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