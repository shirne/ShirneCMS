<?php


namespace app\admin\controller;


use app\admin\validate\PostageValidate;
use app\common\model\PostageModel;
use think\Db;

class ProductPostageController extends BaseController
{
    /**
     * 运费模板列表
     */
    public function index()
    {
        $model = Db::name('postage');
        
        $lists=$model->order('id ASC')->select();
        $this->assign('lists',$lists);
        return $this->fetch();
    }
    
    /**
     * 添加会员组
     */
    public function add()
    {
        if ($this->request->isPost()) {
            
            $data=$this->request->post();
            $validate=new PostageValidate();
            
            if (!$validate->check($data)) {
                
                $this->error($validate->getError());
                exit();
            } else {
                $levelModel=PostageModel::create($data);
                $insertId=$levelModel['id'];
                if ($insertId!==false) {
                    cache('postage', null);
                    user_log($this->mid,'addpostage',1,'添加运费模板'.$insertId ,'manager');
                    $this->success(lang('Add success!'), url('productPostage/index'));
                } else {
                    $this->error(lang('Add failed!'));
                }
            }
        }
        $this->assign('model',[]);
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
            $validate=new PostageValidate();
            
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }else{
                $model=PostageModel::get($id);
                if ($model->allowField(true)->save($data)) {
                    cache('postage', null);
                    user_log($this->mid,'updatepostage',1,'修改运费模板'.$id ,'manager');
                    $this->success(lang('Update success!'), url('productPostage/index'));
                } else {
                    $this->error(lang('Update failed!'));
                }
            }
        }
        $model = PostageModel::get($id);
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
        $count=Db::name('product')->where('postage_id',$id)->count();
        if($count>0){
            $this->error("该模板尚有产品使用,不能删除");
        }
        $model = Db::name('postage');
        $result = $model->delete($id);
        if($result){
            cache('postage', null);
            $this->success(lang('Delete success!'), url('productPostage/index'));
        }else{
            $this->error(lang('Delete failed!'));
        }
    }
}