<?php
namespace app\admin\controller;
/**
 * 邀请码管理
 */
class InviteController extends BaseController
{
    /**
     * 用户列表
     */
    public function index($key="")
    {
        $model = M('invite_code');
        $where=array();
        if(!empty($key )){
            $where['username'] = array('like',"%$key%");
            $where['email'] = array('like',"%$key%");
            $where['_logic'] = 'or';
        }

        $this->pagelist($model,$where,'id DESC');
        $this->assign('levels',getMemberLevels());
        $this->display();     
    }


    /**
     * 生成邀请码
     */
    public function add()
    {
        if (IS_POST) {
            //如果用户提交数据
            $model = D("invite_code");
            $mem_id=I('member_id/d');
            $level_id=I('level_id/d');
            $length=I('length/d');
            $number=I('number/d');
            $date=I('valid_date');
            if($length<8 || $length>16)$this->error('激活码长度需在8-16位之间');
            if($number>1000)$this->error('每次生成数量在 1000以内');
            $member=M('member')->where(array('id'=>$mem_id))->find();
            if(empty($member))$this->error('指定的会员不存在');
            $invalid=0;
            if(!empty($date)){
                $d=strtotime($date);
                if($d)$invalid=$d;
            }


            $data=array();
            $data['member_id']=$mem_id;
            $data['level_id']=$level_id;
            $data['invalid_at']=$invalid;
            $data['is_lock']=0;
            $data['create_at']=time();
            $data['member_use']=0;
            $data['use_at']=0;
            for ($i=0;$i<$number;$i++){
                $data['code']=$this->create($length);
                $model->add($data);
            }
            $this->success("生成成功", U('Invite/index'));
        }else{
            $this->assign('levels',getMemberLevels());
            $this->display();
        }
    }
    /**
     * 转赠送
     */
    public function update()
    {
        //默认显示添加表单
        if (!IS_POST) {
            $model = M('invite_code')->find(I('id/d'));
            $this->assign('model',$model);
            $this->display();
        }
        if (IS_POST) {
            $model = D("invite_code");
            if (!$model->create()) {
                $this->error($model->getError());
            }else{
                $data=array();
                //更新
                if ($model->save($data)) {
                    $this->success("转赠成功", U('Invite/index'));
                } else {
                    $this->error("转赠失败");
                }        
            }
        }
    }
    protected function create($length){
        $c=random_str($length);
        $r='';
        for($j=0;$j<$length;$j+=4){
            $r .= substr($c,$j,4).'-';
        }
        return trim($r,'-');
    }
}
