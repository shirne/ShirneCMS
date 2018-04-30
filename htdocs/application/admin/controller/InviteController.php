<?php
namespace app\admin\controller;
use think\Db;

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
        $model = Db::name('invite_code');
        $where=array();
        if(!empty($key )){
            $where[] = array('code','like',"%$key%");
        }

        $lists=$model->where($where)->paginate(15);
        $this->assign('lists',$lists);
        $this->assign('page',$lists->render());
        $this->assign('levels',getMemberLevels());
        return $this->fetch();
    }


    /**
     * 生成邀请码
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
            $member=Db::name('member')->where(array('id'=>$mem_id))->find();
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
            }
            $this->success("生成成功", url('Invite/index'));
        }else{
            $this->assign('levels',getMemberLevels());
            return $this->fetch();
        }
    }
    /**
     * 转赠送 功能暂无
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
    protected function create($length){
        $c=random_str($length);
        $r='';
        for($j=0;$j<$length;$j+=4){
            $r .= substr($c,$j,4).'-';
        }
        return trim($r,'-');
    }
}
