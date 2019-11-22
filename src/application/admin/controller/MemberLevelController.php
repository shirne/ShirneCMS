<?php

namespace app\admin\controller;

use app\common\model\MemberLevelModel;
use app\admin\validate\MemberLevelValidate;
use think\Db;

/**
 * 会员组管理
 * Class MemberLevelController
 * @package app\admin\controller
 */
class MemberLevelController extends BaseController
{
    /**
     * 会员组列表
     */
    public function index()
    {
        $model = Db::name('memberLevel');

        $lists=$model->order('sort ASC,level_id ASC')->select();
        $this->assign('lists',$lists);
        return $this->fetch();
    }

    /**
     * 代理级别管理
     */
    public function agent()
    {

        $names=['普通','初级','中级','高级'];
        $snames=['普','初','中','高'];
        $styles=['secondary','info','warning','danger'];

        if($this->request->isPost()){
            $agents=$this->request->post('agents');
            $default=$this->request->post('is_default');
            foreach ($agents as $id=>$data){
                if($default==$id){
                    $data['is_default']=1;
                }else{
                    $data['is_default']=0;
                }
                $data['style']=$styles[$id-1];
                Db::name('memberAgent')->where('id',$id)->update($data);
            }
            MemberAgentModel::clearCacheData();
            $this->success('保存成功！',url('memberLevel/agent'));
            
        }

        $model = Db::name('memberAgent');
        $lists=$model->order('id ASC')->select();
        $count=count($names);
        if(count($lists)<$count){
            $lists=array_index($lists,'id');
            for($i=0;$i<$count;$i++){
                if(!isset($lists[$i+1])){
                    $model->insert([
                        'id'=>$i+1,
                        'name'=>$names[$i],
                        'short_name'=>$snames[$i],
                        'style'=>$styles[$i],
                        'is_default'=>$i==0?1:0,
                        'recom_count'=>0,
                        'team_count'=>0,
                        'sale_award'=>0,
                        'global_sale_award'=>0
                    ]);
                }
            }
            MemberAgentModel::clearCacheData();
            $lists=$model->order('id ASC')->select();
        }
        $this->assign('lists',$lists);
        return $this->fetch();
    }
    
    /**
     * 添加会员组
     */
    public function add()
    {
        if ($this->request->isPost()) {
            //如果用户提交数据
            $data=$this->request->post();
            $validate=new MemberLevelValidate();
            $validate->setId();
            if (!$validate->check($data)) {
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                $this->error($validate->getError());
                exit();
            } else {
                $levelModel=MemberLevelModel::create($data);
                $insertId=$levelModel['id'];
                if ($insertId!==false) {
                    cache('levels', null);
                    user_log($this->mid,'addlevel',1,'添加会员组'.$insertId ,'manager');
                    $this->success(lang('Add success!'), url('memberLevel/index'));
                } else {
                    $this->error(lang('Add failed!'));
                }
            }
        }
        $this->assign('model',array(
            'commission_layer'=>3
        ));
        $this->assign('styles',getTextStyles());
        return $this->fetch('update');
    }

    /**
     * 修改会员组
     */
    public function update($id)
    {
        $id = intval($id);
        if ($this->request->isPost()) {
            $data=$this->request->post();
            $validate=new MemberLevelValidate();
            $validate->setId($id);
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }else{
                $model=MemberLevelModel::get($id);
                if ($model->allowField(true)->save($data)) {
                    cache('levels', null);
                    user_log($this->mid,'updatelevel',1,'修改会员组'.$id ,'manager');
                    $this->success(lang('Update success!'), url('memberLevel/index'));
                } else {
                    $this->error(lang('Update failed!'));
                }
            }
        }
        $model = MemberLevelModel::get($id);
        $this->assign('model',$model);
        $this->assign('styles',getTextStyles());
        return $this->fetch();
    }

    /**
     * 删除会员组
     * @param $id
     */
    public function delete($id)
    {
        $id = intval($id);
        $count=Db::name('Member')->where('level_id',$id)->count();
        if($count>0){
            $this->error("该分组尚有会员,不能删除");
        }
        $model = Db::name('memberLevel');
        $result = $model->delete($id);
        if($result){
            cache('levels', null);
            $this->success(lang('Delete success!'), url('memberLevel/index'));
        }else{
            $this->error(lang('Delete failed!'));
        }
    }
}