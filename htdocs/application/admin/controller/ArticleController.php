<?php
namespace app\admin\controller;

use app\common\model\ArticleModel;
use app\admin\validate\ArticleValidate;
use app\admin\validate\ImagesValidate;
use app\common\facade\CategoryFacade;
use app\common\model\ArticleCommentModel;
use think\Db;
use think\Exception;
use think\Response;

/**
 * 文章管理
 * Class ArticleController
 * @package app\admin\controller
 */
class ArticleController extends BaseController
{
    public function search($key='',$cate=0,$type=0){
        $model=Db::name('article')
            ->where('status',1);
        if(!empty($key)){
            $model->where('id|title','like',"%$key%");
        }
        if($cate>0){
            $model->whereIn('cate_id',CategoryFacade::getSubCateIds($cate));
        }
        if(!empty($type)){
            $model->where('type',$type);
        }

        $lists=$model->field('id,title,cover,description,create_time')
            ->order('id ASC')->limit(10)->select();
        return json(['data'=>$lists,'code'=>1]);
    }

    /**
     * 文章列表
     * @param string $key
     * @param int $cate_id
     * @return mixed|\think\response\Redirect
     */
    public function index($key="",$cate_id=0)
    {
        if($this->request->isPost()){
            return redirect(url('',['cate_id'=>$cate_id,'key'=>base64_encode($key)]));
        }
        $key=empty($key)?"":base64_decode($key);
        $model = Db::view('article','*')->view('category',['name'=>'category_name','title'=>'category_title'],'article.cate_id=category.id','LEFT')
            ->view('manager',['username'],'article.user_id=manager.id','LEFT');
        if(!empty($key)){
            $model->whereLike('article.title|manager.username|category.title',"%$key%");
        }
        if($cate_id>0){
            $model->whereIn('article.cate_id',CategoryFacade::getSubCateIds($cate_id));
        }

        $lists=$model->order('id DESC')->paginate(10);
        $this->assign('lists',$lists);
        $this->assign('page',$lists->render());
        $this->assign('types',getArticleTypes());
        $this->assign('keyword',$key);
        $this->assign('cate_id',$cate_id);
        $this->assign("category",CategoryFacade::getCategories());

        return $this->fetch();
    }
    
    public function set_increment($incre){
        $this->setAutoIncrement('article',$incre);
    }

    /**
     * 添加
     * @param int $cid
     * @return mixed
     */
    public function add($cid=0){
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $validate = new ArticleValidate();

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
                    $this->success(lang('Add success!'), url('Article/index'));
                } else {
                    delete_image($data['cover']);
                    $this->error(lang('Add failed!'));
                }
            }
        }
        $model=array('type'=>1,'cate_id'=>$cid);
        $this->assign("category",CategoryFacade::getCategories());
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
    public function edit($id)
    {
        $id = intval($id);

        if ($this->request->isPost()) {
            $data=$this->request->post();
            $validate=new ArticleValidate();

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
                if ($model->allowField(true)->save($data)) {
                    delete_image($delete_images);
                    user_log($this->mid, 'updatearticle', 1, '修改文章 ' . $id, 'manager');
                    $this->success("编辑成功", url('Article/index'));
                } else {
                    delete_image($data['cover']);
                    $this->error("编辑失败");
                }
            }
        }else{

            $model = ArticleModel::get($id);
            if(empty($model)){
                $this->error('文章不存在');
            }
            $this->assign("category",CategoryFacade::getCategories());
            $this->assign('article',$model);
            $this->assign('types',getArticleTypes());
            $this->assign('id',$id);
            return $this->fetch();
        }
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
            $this->success(lang('Delete success!'), url('Article/index'));
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
            $this -> success("发布成功", url('Article/index'));
        } elseif ($result && $data['status'] === 0) {
            user_log($this->mid,'cancelarticle',1,'撤销文章 '.$id ,'manager');
            $this -> success("撤销成功", url('Article/index'));
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
    public function imagelist($aid, $key=''){
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
    public function imageadd($aid){
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
                $url=url('article/imagelist',array('aid'=>$aid));
                if ($model->insert($data)) {
                    $this->success(lang('Add success!'),$url);
                } else {
                    delete_image($data['image']);
                    $this->error(lang('Add failed!'));
                }
            }
        }
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
    public function imageupdate($id)
    {
        $id = intval($id);

        if ($this->request->isPost()) {
            $data=$this->request->post();
            $validate=new ImagesValidate();

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }else{
                $model = Db::name("ArticleImages");
                $url=url('article/imagelist',array('aid'=>$data['article_id']));
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
        }else{
            $model = Db::name('ArticleImages')->where('id', $id)->find();
            if(empty($model)){
                $this->error('图片不存在');
            }

            $this->assign('model',$model);
            $this->assign('aid',$model['article_id']);
            $this->assign('id',$id);
            return $this->fetch();
        }
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
            $this->success(lang('Delete success!'), url('article/imagelist',array('aid'=>$aid)));
        }else{
            $this->error(lang('Delete failed!'));
        }
    }


    /**
     * 评论管理
     * @param int $id
     * @param string $key
     * @return mixed
     */
	public function comments($id=0,$key=''){
        $model = Db::view('articleComment','*')
            ->view('member',['username','level_id','avatar'],'member.id=articleComment.member_id','LEFT')
            ->view('article',['title'=>'article_title','cate_id','cover'],'article.id=articleComment.article_id','LEFT')
            ->view('category',['name'=>'category_name','title'=>'category_title'],'article.cate_id=category.id','LEFT');
        $where=array();
        if($id>0){
            $where[]=['article_id',$id];
        }
        if(!empty($key)){
            $where[]=['article.title|category.title','like',"%$key%"];
        }

        $lists=$model->where($where)->paginate(10);

        $this->assign('lists',$lists);
        $this->assign('page',$lists->render());
        $this->assign('keyword',$key);
        $this->assign('article_id',$id);
        $this->assign("category",CategoryFacade::getCategories());

        return $this->fetch();
    }

    /**
     * 评论查看/回复
     * @param $id
     * @return mixed
     */
    public function commentview($id){
	    $model=Db::name('articleComment')->find($id);
	    if(empty($model)){
	        $this->error('评论不存在');
        }
	    if($this->request->isPost()){
            $data=$this->request->post();
            $data['reply_id']=$id;
            $data['group_id']=empty($model['group_id'])?$model['id']:$model['group_id'];
            $data['status']=1;
            ArticleCommentModel::create($data);
            $this->success('回复成功');

        }
        $article=Db::name('article')->find($model['article_id']);
        $category=Db::name('category')->find($article['cate_id']);
        $member=Db::name('member')->find($model['member_id']);

        $this->assign('model',$model);
        $this->assign('article',$article);
        $this->assign('category',$category);
        $this->assign('member',$member);
        return $this->fetch();
    }

    /**
     * 评论状态
     * @param $id
     * @param int $type
     */
    public function commentstatus($id,$type=1)
    {
        $data['status'] = $type==1?1:2;

        $result = Db::name('articleComment')->where('id','in',idArr($id))->update($data);
        if ($result && $data['status'] === 1) {
            user_log($this->mid,'auditcomment',1,'审核评论 '.$id ,'manager');
            $this -> success("审核成功", url('Article/comments'));
        } elseif ($result && $data['status'] === 2) {
            user_log($this->mid,'hidecomment',1,'隐藏评论 '.$id ,'manager');
            $this -> success("评论已隐藏", url('Article/comments'));
        } else {
            $this -> error("操作失败");
        }
    }

    /**
     * 删除评论
     * @param $id
     */
    public function commentdelete($id)
    {
        $model = Db::name('articleComment');
        $result = $model->where('id','in',idArr($id))->delete();
        if($result){
            user_log($this->mid,'deletecomment',1,'删除评论 '.$id ,'manager');
            $this->success(lang('Delete success!'), url('Article/comments'));
        }else{
            $this->error(lang('Delete failed!'));
        }
    }
}
