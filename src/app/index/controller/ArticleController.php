<?php

namespace app\index\controller;

use app\common\facade\CategoryFacade;
use app\common\model\ArticleCommentModel;
use app\common\model\ArticleModel;
use app\common\validate\ArticleCommentValidate;
use shirne\third\Aliyun;
use \think\facade\Db;
/**
 * 文章
 */
class ArticleController extends BaseController{

    protected $categries;
    protected $category;
    protected $topCategory;
    protected $categoryTree;
    protected $pagesize=12;

    public function initialize()
    {
        parent::initialize();
        $this->assign('navmodel','article');
    }

    public function index($name=""){
        $this->category($name);
        $model=Db::view('article','*')
            ->view('category',['name'=>'category_name','title'=>'category_title'],'article.cate_id=category.id','LEFT')
            ->view('manager',['username'],'manager.id=article.user_id','LEFT');

        $model->where('article.status',1);
        if(!empty($this->category)){
            $this->seo($this->category['title']);
            $model->whereIn('article.cate_id',CategoryFacade::getSubCateIds($this->category['id']));
        }else{
            $this->seo(lang('News'));
        }

        $lists=$model->order('article.create_time DESC,article.id DESC')->paginate($this->pagesize);
        $lists->each(function($item){
            if(!empty($item['prop_data'])){
                $item['prop_data']=json_decode($item['prop_data'],true);
            }
            $item['prop_data']=[];
            return $item;
        });

        $this->assign('lists', $lists);
        $this->assign('page',$lists->render());
        if(!empty($this->categoryTree)){
            for($i=count($this->categoryTree)-1;$i>=0;$i--){
                if($this->categoryTree[$i]['use_template']){
                    return $this->fetch($this->categoryTree[$i]['name'].'/index');
                }
            }
        }

        return $this->fetch();
    }

    public function view($id){
        $article = ArticleModel::find($id);
        if(empty($article)){
            return $this->errorPage(lang('Article not exists!'));
        }
        $this->seo($article['title']);
        $this->category($article['cate_id']);

        $article->inc('views',1);
        $article['views']+=$article['v_views'];
        $article['digg']+=$article['v_digg'];

        $this->assign('article', $article);
        $this->assign('images',Db::name('ArticleImages')->where('article_id',$article['id'])->select());
        if(!empty($this->categoryTree)){
            for($i=count($this->categoryTree)-1;$i>=0;$i--){
                if($this->categoryTree[$i]['use_template']){
                    return $this->fetch($this->categoryTree[$i]['name'].'/view');
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
            $this->error(lang('Arguments error!'));
        }
        if($this->request->isPost()){
            $this->checkSubmitRate(2);
            $data=$this->request->post(['email','is_anonymous','content','reply_id']);
            if($this->config['anonymous_comment']==0 && !$this->isLogin){
                $this->error('请登陆后评论');
            }
            $data['article_id']=$id;
            $validate=new ArticleCommentValidate();
            if(!$validate->check($data)){
                $this->error($validate->getError());
            }else{
                $data['member_id']=$this->userid;
                if(!empty($data['member_id'])){
                    $data['email']=$this->user['email'];
                    $data['nickname']=$this->user['nickname']?:$this->user['username'];

                    //检测会员评论频率
                    $check = ArticleCommentModel::checkSubmitByMember($id, $this->userid);
                }else{
                    if(empty($data['email'])){
                        $this->error('请填写邮箱');
                    }

                    $check = ArticleCommentModel::checkSubmitByIP($id);
                }
                if(!$check){
                    $this->error('系统繁忙,请稍后再提交评论');
                }
                if(!empty($data['reply_id'])){
                    $reply=Db::name('ArticleComment')->find($data['reply_id']);
                    if(empty($reply)){
                        $this->error('回复的评论不存在');
                    }
                    $data['group_id']=empty($reply['group_id'])?$reply['id']:$reply['group_id'];
                }
                $data['content']=preg_replace_callback('/\[([^\]]+)\]\([^\)]+\)/',function($matches){
                    return $matches[0];
                },$data['content']);

                $aliyun = new Aliyun($this->config);
                $detected = $aliyun->greenScan($data['content']);
                if($detected < 0){
                    $this->error('系统检测到您的评论含有非法或无意义内容,请重新组织内容');
                }
                $data['status']=$detected;
                
                $model=ArticleCommentModel::create($data);
                if($model['id']){
                    $this->success('评论成功'.($detected==0?',请等待管理员审核':''));
                }else{
                    $this->error('评论失败');
                }
            }
        }

        $model=Db::view('articleComment','*')
        ->view('member',['username','realname','avatar'],'member.id=articleComment.member_id','LEFT')
        ->where('article_id',$id);

        if($this->isLogin){
            $model->where(function($query){
                return $query->where('articleComment.status',1)
                ->whereOr('articleComment.member_id',$this->userid);
            });
        }else{
            $model->where('articleComment.status',1);
        }

        $comments=$model->order('articleComment.create_time desc')->paginate(10);

        if($this->request->isAjax()){
            $this->success('','',[
                'comments'=>$comments->all(),
                'page'=>$comments->currentPage(),
                'total'=>$comments->total(),
                'total_page'=>$comments->lastPage(),
            ]);
        }

        $this->seo($article['title']);
        $this->category($article['cate_id']);

        $this->assign('article',$article);
        $this->assign('comments',$comments);
        $this->assign('page',$comments->render());
        if(!empty($this->categoryTree)){
            for($i=count($this->categoryTree)-1;$i>=0;$i--){
                if($this->categoryTree[$i]['use_template']){
                    return $this->fetch($this->categoryTree[$i]['name'].'/comment');
                }
            }
        }
        return $this->fetch();
    }

    private function category($name=''){

        $this->category=CategoryFacade::findCategory($name);
        $this->categoryTree=CategoryFacade::getCategoryTree($name);
        $this->categries=CategoryFacade::getTreedCategory();
        if(empty($this->category)){
            $this->category=['id'=>0,'title'=>'新闻中心'];
            $this->topCategory=$this->category;
        }else{
            $this->topCategory=$this->categoryTree[0];
        }

        $this->assign('category',$this->category);
        $this->assign('topCategory',$this->topCategory);
        $this->assign('categoryTree',$this->categoryTree);
        $this->assign('categories',$this->categries);

        if(!empty($this->categoryTree)) {
            $this->assign('navmodel', 'article-' . $this->categoryTree[0]['name']);

            foreach ($this->categoryTree as $cate){
                if($cate['pagesize']>0){
                    $this->pagesize=intval($cate['pagesize']);
                }
            }
        }
    }
}
