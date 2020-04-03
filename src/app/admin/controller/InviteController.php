<?php
namespace app\admin\controller;

use think\facade\Db;

/**
 * 邀请码管理
 * Class InviteController
 * @package app\admin\controller
 */
class InviteController extends BaseController
{
    /**
     * 邀请码列表
     * @param string $key
     * @return mixed
     */
    public function index($key="")
    {
        $model = Db::view('inviteCode','*')
        ->view('member',['username'],'member.id=inviteCode.member_id','LEFT')
        ->view('member memberUse',['username'=>'use_username'],'memberUse.id=inviteCode.member_use','LEFT');

        if(!empty($key )){
            $key=trim($key);
            $model->whereLike('inviteCode.code|inviteCode.member_id|inviteCode.member_use',"%$key%");
        }

        $lists=$model->paginate(15);
        $this->assign('lists',$lists);
        $this->assign('page',$lists->render());
        $this->assign('levels',getMemberLevels());
        $this->assign('keyword',$key);
        return $this->fetch();
    }


    /**
     * 生成邀请码
     * @return mixed
     */
    public function add()
    {
        if ($this->request->isPost()) {
            //如果用户提交数据
            $model = Db::name("invite_code");
            $mem_id=$this->request->post('member_id/d');
            $level_id=$this->request->post('level_id/d');
            $length=$this->request->post('length/d');
            $number=$this->request->post('number/d');
            $date=$this->request->post('valid_date');

            if($length<8 || $length>16)$this->error('激活码长度需在8-16位之间');
            if($number>1000)$this->error('每次生成数量在 1000以内');
            $member=Db::name('member')->where('id',$mem_id)->find();
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
            $data['create_time']=time();
            $data['member_use']=0;
            $data['use_at']=0;
            for ($i=0;$i<$number;$i++){
                $data['code']=$this->create($length);
                $model->insert($data);
                $model->setOption('data',[]);
            }
            user_log($this->mid,'addinvite',1,'生成邀请码['.$mem_id.','.$level_id.']'.$number.'个','manager');
            $this->success("生成成功", url('Invite/index'));
        }else{
            $this->assign('levels',getMemberLevels());
            return $this->fetch();
        }
    }

    /**
     * 转赠
     * @param $id
     * @return mixed
     */
    public function update($id)
    {
        $id=intval($id);
        if ($this->request->isPost()) {
            $model = Db::name("invite_code");

            $data=array();

            //更新
            if ($model->update($data)) {
                $this->success("转赠成功", url('Invite/index'));
            } else {
                $this->error("转赠失败");
            }
        }

        $model = Db::name('invite_code')->find($id);
        $this->assign('model',$model);
        return $this->fetch();
    }

    /**
     * 生成激活码
     * @param $length
     * @return string
     */
    protected function create($length){
        $c=random_str($length);
        $r='';
        for($j=0;$j<$length;$j+=4){
            $r .= substr($c,$j,4).'-';
        }
        return trim($r,'-');
    }
}
