<?php


namespace app\admin\controller;


use app\admin\validate\BoothValidate;
use app\common\model\BoothModel;
use think\Db;

class BoothController extends BaseController
{
    /**
     * 管理
     * @param $key
     * @return mixed
     * @throws \Throwable
     */
    public function index($key=''){
        $model = Db::name('Booth');
        if(!empty($key)){
            $model->whereLike('title|flag',"%$key%");
        }
        $lists=$model->order('id DESC')->paginate(15);
        $this->assign('lists',$lists);
        $this->assign('page',$lists->render());
        return $this->fetch();
    }
    
    /**
     * 添加
     * @return mixed
     * @throws \Throwable
     */
    public function add(){
        if ($this->request->isPost()) {
            $data=$this->request->post();
            $validate=new BoothValidate();
            $validate->setId();
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }else{
                $created=BoothModel::create($data);
                if ($created['id']) {
                    $this->success(lang('Add success!'), url('adv/index'));
                } else {
                    $this->error(lang('Add failed!'));
                }
            }
        }
        $model=array('status'=>1);
        $this->assign('model',$model);
        $this->assign('id',0);
        return $this->fetch('update');
    }
    
    /**
     * 修改
     * @param $id
     * @return mixed
     * @throws \Throwable
     */
    public function update($id)
    {
        $id = intval($id);
        $model=BoothModel::get($id);
        if(empty($model) ){
            $this->error('广告组不存在');
        }
        
        if ($this->request->isPost()) {
            $data=$this->request->post();
            $validate=new BoothValidate();
            $validate->setId($id);
            
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }else{
                if(!isset($data['ext_set']))$data['ext_set']=[];
                $updated=$model->allowField(true)->save($data);
                if ($updated) {
                    $this->success(lang('Update success!'), url('adv/index'));
                } else {
                    $this->error(lang('Update failed!'));
                }
            }
        }
        
        $this->assign('model',$model);
        $this->assign('id',$id);
        return $this->fetch();
    }
    
    public function lock($id){
        $booth=BoothModel::get(intval($id));
        if(empty($booth)){
            $this->error('展位不存在');
        }
        $booth->save(['locked'=>1]);
        $this->success('锁定成功');
    }
    
    public function unlock($id){
        $booth=BoothModel::get(intval($id));
        if(empty($booth)){
            $this->error('展位不存在');
        }
        $booth->save(['locked'=>0]);
        $this->success('解锁成功');
    }
    
    /**
     * 删除广告位
     */
    public function delete($id)
    {
        $id = intval($id);
        $force=$this->request->post('force/d',0);
        $model = Db::name('Booth');
        
        $result = $model->delete($id);
        if($result){
            $this->success(lang('Delete success!'), url('adv/index'));
        }else{
            $this->error(lang('Delete failed!'));
        }
    }
}