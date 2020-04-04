<?php


namespace app\admin\controller\shop;

use app\admin\controller\BaseController;
use app\admin\validate\PostageValidate;
use app\common\model\PostageModel;
use think\facade\Db;

class PostageController extends BaseController
{
    /**
     * 运费模板列表
     */
    public function index()
    {
        
        $lists=PostageModel::order('id ASC')->select();
        $this->assign('lists',$lists);
        return $this->fetch();
    }
    
    /**
     * 添加邮费模板
     */
    public function add()
    {
        if ($this->request->isPost()) {
            
            $data=$this->request->post();
            $validate=new PostageValidate();
            
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                $areas = $data['areas'];
                unset($data['areas']);
                if(empty($data['specials']))$data['specials']=[];
                $levelModel=PostageModel::create($data);
                $insertId=$levelModel['id'];
                if ($insertId!==false) {
                    PostageModel::updateAreas($areas,$insertId);
                    cache('postage', null);
                    user_log($this->mid,'addpostage',1,'添加运费模板'.$insertId ,'manager');
                    $this->success(lang('Add success!'), url('shop.postage/index'));
                } else {
                    $this->error(lang('Add failed!'));
                }
            }
        }
        $counts = Db::name('postage')->where('is_default',1)->count();
        $this->assign('model',[
            'is_default'=>$counts<1?1:0,
            'area_type'=>0,
            'calc_type'=>0
        ]);
        $this->assign('areas',[
            ['id'=>0,'sort'=>0]
        ]);
        $this->assign('express',config('express.'));
        return $this->fetch('update');
    }
    
    /**
     * 修改会员组
     */
    public function update($id)
    {
        $id = intval($id);
        $model = PostageModel::find($id);
        if(empty($model)){
            $this->error('运费模板不存在');
        }
        if ($this->request->isPost()) {
            $data=$this->request->post();
            $validate=new PostageValidate();
            
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }else{
                if(empty($data['specials']))$data['specials']=[];
                if ($model->allowField(true)->save($data)) {
                    PostageModel::updateAreas($data['areas'],$id);
                    cache('postage', null);
                    user_log($this->mid,'updatepostage',1,'修改运费模板'.$id ,'manager');
                    $this->success(lang('Update success!'), url('shop.postage/index'));
                } else {
                    $this->error(lang('Update failed!'));
                }
            }
        }
        $this->assign('model',$model);
        $this->assign('areas',$model->getAreas());
        $this->assign('express',config('express.'));
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
        $result = Db::name('postage')->where('id',$id)->delete();
        if($result){
            Db::name('postageArea')->where('postage_id',$id)->delete();
            cache('postage', null);
            $this->success(lang('Delete success!'), url('shop.postage/index'));
        }else{
            $this->error(lang('Delete failed!'));
        }
    }
}