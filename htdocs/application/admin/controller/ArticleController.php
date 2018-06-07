<?php
namespace app\admin\controller;
use app\admin\model\ArticleModel;
use app\admin\validate\ArticleValidate;
use app\common\facade\CategoryFacade;
use app\common\model\ArticleCommentModel;
use think\Db;
use think\Response;

/**
 * 文章管理
 */
class ArticleController extends BaseController
{
    /**
     * 文章列表
     */
    public function index($key="",$cate_id=0)
    {
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
                }
                unset($data['delete_cover']);
                $data['user_id'] = $this->mid;
                $model=ArticleModel::create($data);
                if ($model->id) {
                    delete_image($delete_images);
                    user_log($this->mid,'addarticle',1,'添加文章 '.$model->id ,'manager');
                    $this->success("添加成功", url('Article/index'));
                } else {
                    $this->error("添加失败");
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
     * 更新文章信息
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
                }
                $model=ArticleModel::get($id);
                if ($model->allowField(true)->save($data)) {
                    delete_image($delete_images);
                    user_log($this->mid, 'updatearticle', 1, '修改文章 ' . $id, 'manager');
                    $this->success("编辑成功", url('Article/index'));
                } else {
                    $this->error("编辑失败");
                }
            }
        }else{

            $model = Db::name('article')->find($id);
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
            $this->success("删除成功", url('Article/index'));
        }else{
            $this->error("删除失败");
        }
    }
	public function push($id,$type=0)
    {
        $data['status'] = $type==1?1:0;

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
     * 文章评论
     * @param int $id
     * @return Response
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

    public function commentview($id){
	    $model=Db::name('articleComment')->find('id');
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
    public function commentdelete($id)
    {
        $model = Db::name('articleComment');
        $result = $model->where('id','in',idArr($id))->delete();
        if($result){
            user_log($this->mid,'deletecomment',1,'删除评论 '.$id ,'manager');
            $this->success("删除成功", url('Article/comments'));
        }else{
            $this->error("删除失败");
        }
    }
}
