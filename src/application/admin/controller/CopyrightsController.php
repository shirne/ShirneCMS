<?php
namespace app\admin\controller;

use app\admin\validate\CopyrightsValidate;
use think\Db;

/**
 * 版权署名管理
 * Class CopyrightsController
 * @package app\admin\controller
 */
class CopyrightsController extends BaseController
{
    /**
     * 版权署名列表
     * @param string $key
     * @return mixed|\think\response\Redirect
     */
    public function index($key="")
    {
        if($this->request->isPost()){
            return redirect(url('',['key'=>base64url_encode($key)]));
        }
        $key=empty($key)?"":base64url_decode($key);
        $model = Db::name('copyrights');
        
        if(!empty($key)){
            $model->whereLike('title',"%$key%");
        }
        $lists=$model->order('ID DESC')->paginate(15);
        $this->assign('lists',$lists);
        $this->assign('page',$lists->render());
        return $this->fetch();
    }

    /**
     * 添加版权署名
     * @return mixed
     */
    public function add(){
        if ($this->request->isPost()) {
            //如果用户提交数据
            $data = $this->request->post();
            $validate=new CopyrightsValidate();
            $validate->setId(0);

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                
                

                if (Db::name('copyrights')->insert($data)) {
                    $this->success(lang('Add success!'), url('copyrights/index'));
                } else {
                    $this->error(lang('Add failed!'));
                }
            }
        }
        $model=array('sort'=>99);
        $this->assign('model',$model);
        $this->assign('id',0);
        return $this->fetch('edit');
    }

    /**
     * 编辑版权署名
     * @param $id
     * @return mixed
     */
    public function edit($id)
    {
        if ($this->request->isPost()) {
            //如果用户提交数据
            $data = $this->request->post();
            $validate=new CopyrightsValidate();
            $validate->setId($id);

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {

                $data['id']=$id;
                if (Db::name('copyrights')->update($data)) {
                    $this->success(lang('Update success!'), url('copyrights/index'));
                } else {
                    $this->error(lang('Update failed!'));
                }
            }
        }

        $model = Db::name('copyrights')->find($id);
        if(empty($model)){
            $this->error('版权署名不存在');
        }
        $this->assign('model',$model);
        $this->assign('id',$id);
        return $this->fetch();
    }

    /**
     * 删除版权署名
     * @param $id
     */
    public function delete($id)
    {
        $id = intval($id);
        $model = Db::name('copyrights');
        $result = $model->delete($id);
        if($result){
            $this->success(lang('Delete success!'), url('copyrights/index'));
        }else{
            $this->error(lang('Delete failed!'));
        }
    }
}
