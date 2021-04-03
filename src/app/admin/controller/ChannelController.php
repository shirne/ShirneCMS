<?php

namespace app\admin\controller;

use app\admin\validate\AdvItemValidate;
use app\admin\validate\ArticleValidate;
use app\admin\validate\CategoryValidate;
use app\admin\validate\ImagesValidate;
use app\common\facade\CategoryFacade;
use app\common\model\AdvGroupModel;
use app\common\model\AdvItemModel;
use app\common\model\ArticleModel;
use app\common\model\CategoryModel;
use PhpOffice\PhpSpreadsheet\Calculation\Category;
use think\facade\Db;
use think\facade\Log;

class ChannelController extends BaseController
{
    protected $channel;
    protected $cates;

    protected function initChannel($channel_id){
        $this->channel = CategoryFacade::findCategory($channel_id);
        $allcates = CategoryFacade::getCategories();
        $this->cates = getSortedCategory($allcates, $channel_id);

        $this->assign('channel_id',$channel_id);
        $this->assign('channel',$this->channel);
        $this->assign('category',$this->cates);
    }

    public function index($channel_id, $keyword = '', $cate_id = 0)
    {
        if($this->request->isPost()){
            return redirect(url('',['channel_id'=>$channel_id,'cate_id'=>$cate_id,'keyword'=>base64url_encode($keyword)]));
        }
        $keyword=empty($keyword)?'':base64url_decode($keyword);
        $this->initChannel($channel_id);
        
        $model = Db::view('article','*')
            ->view('category',['name'=>'category_name','title'=>'category_title'],'article.cate_id=category.id','LEFT')
            ->view('manager',['username'],'article.user_id=manager.id','LEFT')
            ->where('channel_id', $channel_id);
        if(!empty($keyword)){
            $model->whereLike('article.title|manager.username|category.title',"%$keyword%");
        }
        if($cate_id>0){
            $model->whereIn('article.cate_id',CategoryFacade::getSubCateIds($cate_id));
        }
        $lists = $model->paginate(15);
        $cateCounts = $model->setOption('field',['count(article.id) as article_count, cate_id'])->group('cate_id')->select();
        //var_dump($cateCounts);exit;

        $this->assign('lists',$lists);
        $this->assign('page',$lists->render());
        $this->assign('cate_id',$cate_id);
        $this->assign('keyword',$keyword);
        return $this->fetch();
    }

    public function setting($channel_id){
        if($this->request->isPost()){
            $data = $this->request->post();
            $validate=new CategoryValidate();
            $validate->setId($channel_id);

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                if(in_array($data['name'],['admin','api','index','vue','task','user','order','channel','cart','share','auth','article'])){
                    $this->error('频道名不合法');
                }
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

                try{
                    $result=CategoryModel::update($data,['id'=>$channel_id]);
                    if ($result) {
                        delete_image($delete_images);
                        CategoryFacade::clearCache();
                    }
                }catch(\Exception $e){
                    throw $e;
                    Log::record($e->getMessage());
                    delete_image([$data['icon'],$data['image']]);
                    $this->error(lang('Update failed!'));
                }
                $this->success(lang('Update success!'), url('channel/setting',['channel_id'=>$channel_id]));
            }
        }
        $this->initChannel($channel_id);
        return $this->fetch();
    }

    public function banner($channel_id){
        $this->initChannel($channel_id);
        $group = Db::name('AdvGroup')->where('flag','channel_'.$channel_id)->find();
        if(empty($group)){
            $gid = Db::name('AdvGroup')->insert([
                'title'=>$this->channel['title'],
                'flag'=>'channel_'.$channel_id,
                'width'=>'1920',
                'height'=>'',
                'ext_set'=>'',
                'locked'=>1,
                'status'=>1,
                'create_time'=>time(),
                'update_time'=>time()
            ],false,true);
        }else{
            $gid = $group['id'];
        }
        $banners = Db::name('AdvItem')->where('group_id',$gid)->paginate(15);

        $this->assign('gid',$gid);
        $this->assign('lists',$banners);
        return $this->fetch();
    }

    /**
     * 添加
     * @param $gid
     * @return mixed
     * @throws \Throwable
     */
    public function banneradd($channel_id, $gid){
        $group = AdvGroupModel::get($gid);
        if(empty($group)){
            $this->error('广告组不存在');
        }
        
        if ($this->request->isPost()) {
            $data=$this->request->post();
            $validate=new AdvItemValidate();

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }else{
                $uploaded=$this->upload('banner','upload_image');
                if(!empty($uploaded)){
                    $data['image']=$uploaded['url'];
                }elseif($this->uploadErrorCode>102){
                    $this->error($this->uploadErrorCode.':'.$this->uploadError);
                }
                $uploaded=$this->uploadFile('banner','upload_video',2);
                if(!empty($uploaded)){
                    $data['video']=$uploaded['url'];
                }elseif($this->uploadErrorCode>102){
                    $this->error($this->uploadErrorCode.':'.$this->uploadError);
                }
                
                $url=url('channel/banner',array('channel_id'=>$channel_id));
                $data['start_date']=empty($data['start_date'])?0:strtotime($data['start_date']);
                $data['end_date']=empty($data['end_date'])?0:strtotime($data['end_date']);
                if(isset($data['ext'])) {
                    $data['ext_data'] = $data['ext'];
                    unset($data['ext']);
                }
                if(isset($data['elements'])){
                    $data['elements'] = $this->filterElements($data['elements']);
                }
                $model = AdvItemModel::create($data);
                if ($model['id']) {
                    $this->success(lang('Add success!'),$url);
                } else {
                    delete_image($data['image']);
                    $this->error(lang('Add failed!'));
                }
            }
        }
        $this->initChannel($channel_id);
        $model=array('status'=>1,'group_id'=>$gid,'ext'=>[]);
        $this->assign('group',$group);
        $this->assign('model',$model);
        $this->assign('id',0);
        return $this->fetch('bannerupdate');
    }

    /**
     * 修改
     */
    public function bannerupdate($channel_id, $id)
    {
        $id = intval($id);
        $model = Db::name('AdvItem')->where('id', $id)->find();
        if(empty($model)){
            $this->error('广告项不存在');
        }
        $model = AdvGroupModel::fixAdItem($model);
        $group = AdvGroupModel::get($model['group_id']);
        if(empty($group)){
            $this->error('广告组不存在');
        }

        if ($this->request->isPost()) {
            $data=$this->request->post();
            $validate=new AdvItemValidate();

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }else{
                $model = AdvItemModel::where('id',$id)->find();
                $url=url('channel/banner',array('channel_id'=>$channel_id));
                $delete_images=[];
                $uploaded=$this->upload('banner','upload_image');
                if(!empty($uploaded)){
                    $data['image']=$uploaded['url'];
                    $delete_images[]=$data['delete_image'];
                }elseif($this->uploadErrorCode>102){
                    $this->error($this->uploadErrorCode.':'.$this->uploadError);
                }
                unset($data['delete_image']);

                $uploaded=$this->uploadFile('banner','upload_video',2);
                if(!empty($uploaded)){
                    $data['video']=$uploaded['url'];
                }elseif($this->uploadErrorCode>102){
                    $this->error($this->uploadErrorCode.':'.$this->uploadError);
                }
                
                $data['start_date']=empty($data['start_date'])?0:strtotime($data['start_date']);
                $data['end_date']=empty($data['end_date'])?0:strtotime($data['end_date']);
                if(isset($data['ext'])) {
                    $data['ext_data'] = $data['ext'];
                    unset($data['ext']);
                }
                if(isset($data['elements'])){
                    $data['elements'] = $this->filterElements($data['elements']);
                }
                
                if ($model->allowField(true)->save($data)) {
                    delete_image($delete_images);
                    $this->success(lang('Update success!'), $url);
                } else {
                    delete_image($data['image']);
                    $this->error(lang('Update failed!'));
                }
            }
        }
        $this->initChannel($channel_id);
        $this->assign('group',$group);
        $this->assign('model',$model);
        $this->assign('id',$id);
        return $this->fetch();
    }

    private function filterElements($elements){
        $fields=[];
        foreach($elements as $k=>$item){
            if($item['type']=='image'){
                $fields[]="elements_{$k}_image";
            }
        }
        
        $uploaded = $this->batchUpload('banner',$fields);
        if(!empty($uploaded)){
            foreach($uploaded as $k=>$file){
                $newkey = explode('_',$k.'_');
                $newkey = $newkey[1];
                $elements[$newkey]['image']=$file;
            }
        }elseif($this->uploadErrorCode>102){
            $this->error($this->uploadErrorCode.':'.$this->uploadError);
        }
        return array_values($elements);
    }

    public function bannerdelete($id){
        $id = intval($id);
        $result = AdvItemModel::where('id',$id)->delete();
        if($result){
            $this->success(lang('Delete success!'));
        }else{
            $this->error(lang('Delete failed!'));
        }
    }

    public function category($channel_id, $id = 0){
        if($this->request->isPost()){
            $data = $this->request->post();
            $validate=new CategoryValidate();
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
                if(empty($data['pid'])){
                    $data['pid']=$channel_id;
                }

                try{
                    if($id > 0){
                        $result=CategoryModel::update($data,['id'=>$id]);
                    }else{
                        $result=CategoryModel::create($data);
                    }
                    if ($result) {
                        delete_image($delete_images);
                        CategoryFacade::clearCache();
                    }
                }catch(\Exception $e){
                    throw $e;
                    Log::record($e->getMessage());
                    delete_image([$data['icon'],$data['image']]);
                    $this->error($id>0?lang('Update failed!'):lang('Add failed!'));
                }
                $this->success($id>0?lang('Update success!'):lang('Add success!'));
            }
        }
        $cate = CategoryModel::where('id', $id)->find();
        return json(['cate'=>$cate, 'code'=>1]);
    }

    public function category_delete($id){
        $id = intval($id);
        $model = Db::name('Category')->where('id',$id)->find();
        if(empty($model)){
            $this->error('分类不存在');
        }
        $hasson = Db::name('Category')->where('pid',$id)->count();
        if($hasson > 0){
            $this->error('请先删除子类');
        }
        Db::name('Category')->where('id',$id)->delete();
        Db::name('article')->where('cate_id',$id)->update(['cate_id'=>0]);
        CategoryFacade::clearCache();
        $this->success('删除成功！');
    }

    /**
     * 添加
     * @param int $cid
     * @return mixed
     */
    public function add($channel_id, $cid=0){
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $validate = new ArticleValidate();
            $validate->setId(0);

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                $delete_images=[];
                $uploaded = $this->upload('article', 'upload_cover');
                if (!empty($uploaded)) {
                    $data['cover'] = $uploaded['url'];
                    $delete_images[]=$data['delete_cover'];
                }elseif($this->uploadErrorCode>102){
                    $this->error($this->uploadErrorCode.':'.$this->uploadError);
                }
                unset($data['delete_cover']);
                $data['user_id'] = $this->mid;
                if(!empty($data['prop_data'])){
                    $data['prop_data']=array_combine($data['prop_data']['keys'],$data['prop_data']['values']);
                }else{
                    $data['prop_data']=[];
                }
                if(empty($data['description']))$data['description']=cutstr($data['content'],240);
                if(!empty($data['create_time']))$data['create_time']=strtotime($data['create_time']);
                if(empty($data['create_time']))unset($data['create_time']);

                $model=ArticleModel::create($data);
                if ($model->id) {
                    delete_image($delete_images);
                    user_log($this->mid,'addarticle',1,'添加文章 '.$model->id ,'manager');
                    $this->success(lang('Add success!'), url('channel/index',['channel_id'=>$channel_id]));
                } else {
                    delete_image($data['cover']);
                    $this->error(lang('Add failed!'));
                }
            }
        }
        $this->initChannel($channel_id);
        $model=array('type'=>1,'cate_id'=>$cid,'digg'=>0,'views'=>0);
        $this->assign('article',$model);
        $this->assign('types',getArticleTypes());
        $this->assign('id',0);
        return $this->fetch('edit');
    }

    /**
     * 修改
     * @param $id
     * @return mixed
     */
    public function edit($channel_id, $id)
    {
        $id = intval($id);

        if ($this->request->isPost()) {
            $data=$this->request->post();
            $validate=new ArticleValidate();
            $validate->setId($id);

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }else{
                $delete_images=[];
                $uploaded=$this->upload('article','upload_cover');
                if(!empty($uploaded)){
                    $data['cover']=$uploaded['url'];
                    $delete_images[]=$data['delete_cover'];
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
                $model=ArticleModel::get($id);
                try {
                    $model->allowField(true)->save($data);
                    delete_image($delete_images);
                    user_log($this->mid, 'updatearticle', 1, '修改文章 ' . $id, 'manager');
                }catch(\Exception $err){
                    delete_image($data['cover']);
                    $this->error(lang('Update failed: %',[$err->getMessage()]));
                }
                $this->success(lang('Update success!'), url('channel/index',['channel_id'=>$channel_id]));
            }
        }
        $this->initChannel($channel_id);
        $model = ArticleModel::get($id);
        if(empty($model)){
            $this->error('文章不存在');
        }
        
        $this->assign('article',$model);
        $this->assign('types',getArticleTypes());
        $this->assign('id',$id);
        return $this->fetch();
    }

    /**
     * 删除文章
     * @param $id
     */
    public function delete($id)
    {
        $model = Db::name('article');
        $result = $model->whereIn("id",idArr($id))->delete();
        if($result){
            Db::name('articleComment')->whereIn("article_id",idArr($id))->delete();
            Db::name('articleDigg')->whereIn("article_id",idArr($id))->delete();
            Db::name('articleImages')->whereIn("article_id",idArr($id))->delete();
            user_log($this->mid,'deletearticle',1,'删除文章 '.$id ,'manager');
            $this->success(lang('Delete success!'));
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

        $result = Db::name('article')->whereIn("id",idArr($id))->update($data);
        if ($result && $data['status'] === 1) {
            user_log($this->mid,'pusharticle',1,'发布文章 '.$id ,'manager');
            $this -> success("发布成功");
        } elseif ($result && $data['status'] === 0) {
            user_log($this->mid,'cancelarticle',1,'撤销文章 '.$id ,'manager');
            $this -> success("撤销成功");
        } else {
            $this -> error("操作失败");
        }
    }

    /**
     * 图集
     * @param $aid
     * @param $key
     * @return mixed
     * @throws Exception
     */
    public function imagelist($channel_id, $aid, $key=''){
        $this->initChannel($channel_id);
        $model = Db::name('ArticleImages');
        $article=Db::name('Article')->find($aid);
        if(empty($article)){
            $this->error('文章不存在');
        }
        $model->where('article_id',$aid);
        if(!empty($key)){
            $model->where('title','like',"%$key%");
        }
        $lists=$model->order('sort ASC,id DESC')->paginate(15);
        $this->assign('article',$article);
        $this->assign('lists',$lists);
        $this->assign('page',$lists->render());
        $this->assign('aid',$aid);
        return $this->fetch();
    }

    /**
     * 添加图片
     * @param $aid
     * @return mixed
     */
    public function imageadd($channel_id, $aid){
        if ($this->request->isPost()) {
            $data=$this->request->post();
            $validate=new ImagesValidate();

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }else{
                $uploaded=$this->upload('article','upload_image');
                if(!empty($uploaded)){
                    $data['image']=$uploaded['url'];
                }
                $model = Db::name("ArticleImages");
                $url=url('channel/imagelist',array('channel_id'=>$channel_id,'aid'=>$aid));
                if ($model->insert($data)) {
                    $this->success(lang('Add success!'),$url);
                } else {
                    delete_image($data['image']);
                    $this->error(lang('Add failed!'));
                }
            }
        }
        $this->initChannel($channel_id);
        $model=array('status'=>1,'article_id'=>$aid);
        $this->assign('model',$model);
        $this->assign('aid',$aid);
        $this->assign('id',0);
        return $this->fetch('imageupdate');
    }

    /**
     * 修改图片
     * @param $id
     * @return mixed
     */
    public function imageupdate($channel_id, $id)
    {
        $id = intval($id);

        if ($this->request->isPost()) {
            $data=$this->request->post();
            $validate=new ImagesValidate();

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }else{
                $model = Db::name("ArticleImages");
                $url=url('channel/imagelist',array('channel_id'=>$channel_id,'aid'=>$data['article_id']));
                $delete_images=[];
                $uploaded=$this->upload('article','upload_image');
                if(!empty($uploaded)){
                    $data['image']=$uploaded['url'];
                    $delete_images[]=$data['delete_image'];
                }
                unset($data['delete_image']);
                $data['id']=$id;
                if ($model->update($data)) {
                    delete_image($delete_images);
                    $this->success(lang('Update success!'), $url);
                } else {
                    delete_image($data['image']);
                    $this->error(lang('Update failed!'));
                }
            }
        }
        $this->initChannel($channel_id);
        $model = Db::name('ArticleImages')->where('id', $id)->find();
        if(empty($model)){
            $this->error('图片不存在');
        }

        $this->assign('model',$model);
        $this->assign('aid',$model['article_id']);
        $this->assign('id',$id);
        return $this->fetch();
    }

    /**
     * 删除图片
     * @param $aid
     * @param $id
     */
    public function imagedelete($aid,$id)
    {
        $id = intval($id);
        $model = Db::name('ArticleImages');
        $result = $model->delete($id);
        if($result){
            $this->success(lang('Delete success!'));
        }else{
            $this->error(lang('Delete failed!'));
        }
    }
}