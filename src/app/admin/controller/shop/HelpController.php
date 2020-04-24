<?php

namespace app\admin\controller\shop;

use app\admin\controller\BaseController;
use app\common\model\HelpModel;
use app\admin\validate\HelpValidate;
use app\admin\validate\HelpCategoryValidate;
use app\common\facade\HelpCategoryFacade;
use think\facade\Db;

class HelpController extends BaseController
{
    /**
     * 帮助中心
     */
    public function index($key="",$cate_id=0)
    {
        
        if($this->request->isPost()){
            return redirect(url('',['cate_id'=>$cate_id,'key'=>base64_encode($key)]));
        }
        $key=empty($key)?"":base64_decode($key);
        $model = Db::view('help','*')->view('helpCategory',['name'=>'category_name','title'=>'category_title'],'help.cate_id=helpCategory.id','LEFT')
            ->view('manager',['username'],'help.user_id=manager.id','LEFT');
        if(!empty($key)){
            $model->whereLike('help.title|manager.username|helpCategory.title',"%$key%");
        }
        if($cate_id>0){
            $model->whereIn('help.cate_id',HelpCategoryFacade::getSubCateIds($cate_id));
        }

        $lists=$model->order('id DESC')->paginate(10);
        $this->assign('lists',$lists);
        $this->assign('page',$lists->render());
        $this->assign('types',getArticleTypes());
        $this->assign('keyword',$key);
        $this->assign('cate_id',$cate_id);
        $this->assign("category",HelpCategoryFacade::getCategories());

        return $this->fetch();
    }

    public function set_increment($incre){
        $this->setAutoIncrement('help',$incre);
    }

    /**
     * 添加
     * @param int $cid
     * @return mixed
     */
    public function add($cid=0){
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $validate = new HelpValidate();

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                $delete_images=[];
                $uploaded = $this->upload('article', 'upload_image');
                if (!empty($uploaded)) {
                    $data['image'] = $uploaded['url'];
                    $delete_images[]=$data['delete_image'];
                }elseif($this->uploadErrorCode>102){
                    $this->error($this->uploadErrorCode.':'.$this->uploadError);
                }
                unset($data['delete_image']);
                $data['user_id'] = $this->mid;
                if(!empty($data['prop_data'])){
                    $data['prop_data']=array_combine($data['prop_data']['keys'],$data['prop_data']['values']);
                }else{
                    $data['prop_data']=[];
                }
                if(empty($data['description']))$data['description']=cutstr($data['content'],240);
                if(!empty($data['create_time']))$data['create_time']=strtotime($data['create_time']);
                if(empty($data['create_time']))unset($data['create_time']);

                $model=HelpModel::create($data);
                if ($model->id) {
                    delete_image($delete_images);
                    user_log($this->mid,'addhelp',1,'添加帮助 '.$model->id ,'manager');
                    $this->success(lang('Add success!'), url('shop.help/index',['cate_id'=>$cid]));
                } else {
                    delete_image($data['cover']);
                    $this->error(lang('Add failed!'));
                }
            }
        }
        $model=array('type'=>1,'cate_id'=>$cid,'digg'=>0,'views'=>0);
        $this->assign("category",HelpCategoryFacade::getCategories());
        $this->assign('article',$model);
        $this->assign('id',0);
        return $this->fetch('edit');
    }

    /**
     * 修改
     * @param $id
     * @return mixed
     */
    public function edit($id,$cid=0)
    {
        $id = intval($id);

        if ($this->request->isPost()) {
            $data=$this->request->post();
            $validate=new HelpValidate();

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }else{
                $delete_images=[];
                $uploaded=$this->upload('shop','upload_image');
                if(!empty($uploaded)){
                    $data['image']=$uploaded['url'];
                    $delete_images[]=$data['delete_image'];
                }elseif($this->uploadErrorCode>102){
                    $this->error($this->uploadErrorCode.':'.$this->uploadError);
                }
                if(!empty($data['prop_data'])){
                    $data['prop_data']=array_combine($data['prop_data']['keys'],$data['prop_data']['values']);
                }else{
                    $data['prop_data']=[];
                }
                if(empty($data['description']))$data['description']=cutstr($data['content'],240);
                if(!empty($data['create_time']))$data['create_time']=strtotime($data['create_time']);
                if(empty($data['create_time']))unset($data['create_time']);
                $model=HelpModel::find($id);
                try{
                    $model->allowField(true)->save($data);
                    delete_image($delete_images);
                    user_log($this->mid, 'updatehelp', 1, '修改帮助 ' . $id, 'manager');
                }catch(\Exception $err){
                    delete_image($data['image']);
                    $this->error(lang('Update failed: %',[$err->getMessage()]));
                }
                
                $this->success(lang('Update success!'), url('shop.help/index',['cate_id'=>$cid]));
            }
        }

        $model = HelpModel::find($id);
        if(empty($model)){
            $this->error('帮助不存在');
        }
        $this->assign("category",HelpCategoryFacade::getCategories());
        $this->assign('article',$model);
        $this->assign('id',$id);
        return $this->fetch();
    }

    /**
     * 删除帮助
     * @param $id
     */
    public function delete($id)
    {
        $model = Db::name('help');
        $result = $model->whereIn("id",idArr($id))->delete();
        if($result){
            user_log($this->mid,'deletehelp',1,'删除帮助 '.$id ,'manager');
            $this->success(lang('Delete success!'), url('shop.help/index'));
        }else{
            $this->error(lang('Delete failed!'));
        }
    }

    /**
     * 发布
     * @param $id
     * @param int $status
     */
	public function status($id,$status=0)
    {
        $data['status'] = $status==1?1:0;

        $result = Db::name('help')->whereIn("id",idArr($id))->update($data);
        if ($result && $data['status'] === 1) {
            user_log($this->mid,'pushhelp',1,'发布帮助 '.$id ,'manager');
            $this -> success("发布成功", url('shop.help/index'));
        } elseif ($result && $data['status'] === 0) {
            user_log($this->mid,'cancelhelp',1,'撤销帮助 '.$id ,'manager');
            $this -> success("撤销成功", url('shop.help/index'));
        } else {
            $this -> error("操作失败");
        }
    }

    public function category($id = 0){

        if($this->request->isPost()){
            $data=$this->request->post();
            $validate=new HelpCategoryValidate();
            $validate->setId($id);

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                $delete_images=[];
                $iconupload=$this->upload('category','upload_icon');
                if(!empty($iconupload)){
                    $data['icon']=$iconupload['url'];
                    $delete_images[]=$data['delete_icon'];
                }
                $uploaded=$this->upload('category','upload_image');
                if(!empty($uploaded)){
                    $data['image']=$uploaded['url'];
                    $delete_images[]=$data['delete_image'];
                }
                unset($data['delete_icon']);
                unset($data['delete_image']);

                if($id > 0){
                    $result=Db::name('helpCategory')->where('id',$id)->update($data);
                }else{
                    $result=Db::name('helpCategory')->insert($data);
                }

                if ($result) {
                    delete_image($delete_images);
                    HelpCategoryFacade::clearCache();
                    $this->success(lang('Update success!'), url('tender.category/index'));
                } else {
                    delete_image([$data['icon'],$data['image']]);
                    $this->error(lang('Update failed!'));
                }
            }
        }
        $model = Db::name('helpCategory')->find($id);
        if(empty($model)){
            $this->error('分类不存在');
        }
        return json(['data'=>$model,'code'=>1]);
    }

}