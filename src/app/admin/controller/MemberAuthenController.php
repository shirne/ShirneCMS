<?php

namespace app\admin\controller;

use app\common\model\MemberAgentModel;
use app\common\model\MemberAuthenModel;
use app\common\model\MemberLevelModel;
use app\common\model\MemberModel;
use think\Db;

/**
 * 会员认证管理
 * Class MemberAuthenController
 * @package app\admin\controller
 */
class MemberAuthenController extends BaseController
{
    /**
     * 会员认证列表
     */
    public function index()
    {
        $model = Db::view('memberAuthen','*')
            ->view('member',['username','avatar','nickname','level_id','is_agent'],'member.id=memberAuthen.member_id','LEFT');

        $lists=$model->order('status ASC,id ASC')->paginate(15);
        $this->assign('lists',$lists->items());
        $this->assign('page',$lists->render());
        $this->assign('levels',MemberLevelModel::getCacheData());
        $this->assign('agents',MemberAgentModel::getCacheData());
        return $this->fetch();
    }

    /**
     * 审核
     */
    public function update($id)
    {
        $id = intval($id);
        if ($this->request->isPost()) {
            $data=$this->request->post();

            $model=MemberAuthenModel::get($id);
            try{
                $model->allowField(true)->save($data);
                user_log($this->mid,'updatememberauthen',1,'审核升级申请'.$id ,'manager');
            }catch(\Exception $err){
                $this->error(lang('Update failed: %s',[$err->getMessage()]));
            }
            $this->success(lang('Update success!'), url('memberAuthen/index'));
        }
        $model = MemberAuthenModel::get($id);
        if(empty($model)){
            $this->error('申请资料不存在');
        }

        $this->assign('model',$model);
        $this->assign('member',MemberModel::get($model['member_id']));
        return $this->fetch();
    }

    /**
     * 删除会员认证
     * @param $id
     */
    public function delete($id)
    {
        $id = intval($id);
        $model = Db::name('memberAuthen');
        $result = $model->delete($id);
        if($result){
            $this->success(lang('Delete success!'), url('memberAuthen/index'));
        }else{
            $this->error(lang('Delete failed!'));
        }
    }
}