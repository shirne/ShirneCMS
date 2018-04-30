<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirn
 * Date: 2016/9/10
 * Time: 16:13
 */

namespace app\index\controller;


use app\common\validate\MemberValidate;
use think\Db;

class MemberController extends AuthController
{
    public function initialize()
    {
        parent::initialize();
    }

    /**
     * 会员中心
     */
    public function index(){
        $this->display();
    }

    /**
     * 个人资料
     */
    public function profile(){
        if($this->request->isPost()){
            $data=$this->request->only(['email','mobile','gender'],'post');
            $validate=new MemberValidate();
            $validate->setId($this->userid);
            if(!$validate->check($data)){
                $this->error($validate->getError());
            }else{
                $data['id']=$this->userid;
                Db::name('Member')->update($data);
                $this->success('保存成功',url('profile'));
            }
        }

        $this->display();
    }

    /**
     * 修改头像
     */
    public function avatar(){

        $this->display();
    }


    /**
     * 安全中心
     */
    public function security(){

    }
}