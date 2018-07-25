<?php

namespace app\index\controller;

use app\common\facade\CategoryFacade;
use app\common\model\ArticleCommentModel;
use app\common\validate\ArticleCommentValidate;
use \think\Db;
/**
 * 文章
 */
class ArticleController extends BaseController{

    protected $categries;
    protected $category;
    protected $categotyTree;

    public function initialize()
    {
        parent::initialize();
        $this->assign('navmodel','article');
    }

    public function index($name=""){
        $this->category($name);

        $where=array();
        if(!empty($this->category)){
            $this->seo($this->category['title']);
            $where[]=array('article.cate_id','in',CategoryFacade::getSubCateIds($this->category['id']));
        }else{
            $this->seo("新闻中心");
        }

        $model=Db::view('article','*')
            ->view('category',['name'=>'category_name','title'=>'category_title'],'article.cate_id=category.id','LEFT')
            ->view('manager',['username'],'manager.id=article.user_id','LEFT')
            ->where($where)
            ->paginate(10);

        $this->assign('lists', $model);
        $this->assign('page',$model->render());
        if(!empty($this->categotyTree)){
            for($i=count($this->categotyTree)-1;$i>=0;$i--){
                if($this->categotyTree[$i]['use_template']){
                    return $this->fetch($this->categotyTree[$i]['name'].'/index');
                }
            }
        }

        return $this->fetch();
    }

    public function view($id){
        $article = Db::name('article')->find($id);
        if(empty($article)){
            $this->error('文章不存在');
        }
        $this->seo($article['title']);
        $this->category($article['cate_id']);

        $this->assign('article', $article);
        $this->assign('images',Db::name('ArticleImages')->where('article_id',$article['id'])->select());
        if(!empty($this->categotyTree)){
            for($i=count($this->categotyTree)-1;$i>=0;$i--){
                if($this->categotyTree[$i]['use_template']){
                    return $this->fetch($this->categotyTree[$i]['name'].'/view');
                }
            }
        }
        return $this->fetch();
    }
    public function notice($id){
        $article = Db::name('notice')->find($id);
        $this->seo($article['title']);
        $this->category();

        $this->assign('article', $article);
        return $this->fetch();
    }
    public function comment($id){
        $article = Db::name('article')->find($id);
        if(empty($article)){
            $this->error('参数错误');
        }
        if($this->request->isPost()){
            $data=$this->request->only('article_id,email,is_anonymous,content,reply_id','POST');
            $validate=new ArticleCommentValidate();
            if(!$validate->check($data)){
                $this->error($validate->getError());
            }else{
                $data['member_id']=$this->userid;
                if(!empty($data['member_id'])){
                    $data['email']=$this->user['email'];
                }else{
                    if(empty($data['email'])){
                        $this->error('请填写邮箱');
                    }
                }
                if(!empty($data['reply_id'])){
                    $reply=Db::name('ArticleComment')->find($data['reply_id']);
                    if(empty($reply)){
                        $this->error('回复的评论不存在');
                    }
                    $data['group_id']=empty($reply['group_id'])?$reply['id']:$reply['group_id'];
                }
                $model=ArticleCommentModel::create($data);
                if($model['id']){
                    $this->success('评论成功');
                }else{
                    $this->error('评论失败');
                }
            }
        }

        $comments=Db::view('articleComment','*')
        ->view('member',['username','realname'],'member.id=articleComment.member_id','LEFT')
        ->where('article_id',$id)->paginate(10);

        $this->assign('comments',$comments);
        $this->assign('page',$comments->render());
        return $this->fetch();
    }

    private function category($name=''){

        $this->category=CategoryFacade::findCategory($name);
        $this->categotyTree=CategoryFacade::getCategoryTree($name);
        $this->categries=CategoryFacade::getTreedCategory();
        if(empty($this->category)){
            $this->category=['id'=>0,'title'=>'新闻中心'];
        }

        $this->assign('category',$this->category);
        $this->assign('categotyTree',$this->categotyTree);
        $this->assign('categories',$this->categries);

        if(!empty($this->categotyTree)) {
            $this->assign('navmodel', 'article-' . $this->categotyTree[0]['name']);
        }
    }
}
