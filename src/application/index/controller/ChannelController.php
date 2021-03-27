<?php

namespace app\index\controller;

use app\common\facade\CategoryFacade;
use app\common\model\ArticleCommentModel;
use app\common\model\ArticleModel;
use app\common\model\MemberFavouriteModel;
use app\common\validate\ArticleCommentValidate;
use shirne\third\Aliyun;
use \think\Db;
/**
 * 文章
 */
class ChannelController extends BaseController{

    protected $categries;
    protected $category;
    protected $channel;
    protected $categoryTree;

    protected $template_dir = '';

    protected $pagesize=12;

    public function initialize()
    {
        parent::initialize();
        $this->assign('navmodel','article');
    }

    public function index($channel_name=""){
        $currentChannel = CategoryFacade::findCategory($channel_name);
        if(empty($currentChannel)){
            return $this->errorPage(lang('Page not exists!'));
        }
        if($currentChannel['channel_mode'] == 0){
            return $this->list($channel_name, $channel_name);
        }elseif($currentChannel['channel_mode'] == 1){
            $subCates = CategoryFacade::getSubCategory($currentChannel['id']);
            if(!empty($subCates)){
                return $this->view($channel_name, $subCates[0]['name']);
            }
            return $this->view($channel_name, $channel_name);
        }
        $this->category($channel_name);

        return $this->fetch($this->template_dir.'/index');
    }

    public function list($channel_name, $cate_name=null){
        $this->category(empty($cate_name)?$channel_name:$cate_name);
        if($this->channel['channel_mode'] == 1){
            return $this->view($channel_name, $cate_name);
        }
        $model=Db::view('article','*')
            ->view('category',['name'=>'category_name','title'=>'category_title'],'article.cate_id=category.id','LEFT')
            ->view('manager',['username'],'manager.id=article.user_id','LEFT');

        $model->where('article.status',1);
        if(!empty($this->category)){
            $this->seo($this->category['title'],$this->category['keywords'],$this->category['description']);
            $model->whereIn('article.cate_id',CategoryFacade::getSubCateIds($this->category['id']));
        }else{
            $this->seo(lang('News'));
        }

        $lists=$model->order('article.create_time DESC,article.id DESC')->paginate($this->pagesize);
        $lists->each(function($item){
            if(!empty($item['prop_data'])){
                $item['prop_data']=force_json_decode($item['prop_data'],true);
            }
            return $item;
        });
        $this->assign('lists', $lists);
        $this->assign('page',$lists->render());

        return $this->fetch($this->template_dir.'/list');
    }

    public function view($channel_name, $cate_name = '', $article_name = ''){
        $this->category(empty($cate_name)?$channel_name:$cate_name);
        if(is_int($article_name)){
            $id = intval($article_name);
            $article = ArticleModel::where('id', $id)->where('status',1)->find();
        }elseif(!empty($article_name)){
            $article = ArticleModel::where('name', $article_name)->where('status',1)->find();
        }elseif($this->channel['channel_mode'] == 1){
            $article = ArticleModel::where('cate_id', $this->category['id'])->where('status',1)->find();
        }
        
        if(empty($article)){
            return $this->errorPage(lang('Article not exists!'));
        }
        $this->seo($article['title']);

        $article->setInc('views',1);
        $article['views']+=$article['v_views'];
        $article['digg']+=$article['v_digg'];

        $this->assign('article', $article);
        $this->assign('images',Db::name('ArticleImages')->where('article_id',$article['id'])->select());
        
        return $this->fetch($this->template_dir.'/'.(empty($article['template'])?'view':$article['template']));
    }
    
    public function comment($channel_name, $cate_name, $article_name){
        $this->category(empty($cate_name)?$channel_name:$cate_name);
        if(is_int($article_name)){
            $id = intval($article_name);
            $article = ArticleModel::where('id', $id)->find();
        }elseif(!empty($article_name)){
            $article = ArticleModel::where('name', $article_name)->find();
        }
        if(empty($article)){
            $this->error(lang('Arguments error!'));
        }
        if($this->request->isPost()){
            $this->checkSubmitRate(2);
            $data=$this->request->only('email,is_anonymous,content,reply_id','POST');
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
                'comments'=>$comments->items(),
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
        
        return $this->fetch($this->template_dir.'/comment');
    }

    private function category($name='')
    {
        if(!empty($this->category) && $this->category['name']==$name){
            return;
        }

        $this->category=CategoryFacade::findCategory($name);
        if(empty($this->category)){
            $this->errorPage('页面不存在');
        }
        $this->categoryTree=CategoryFacade::getCategoryTree($name);
        $this->categries=CategoryFacade::getTreedCategory();
        $this->channel=$this->categoryTree[0];

        $this->assign('category',$this->category);
        $this->assign('channel',$this->channel);
        $this->assign('categoryTree',$this->categoryTree);
        $this->assign('categories',$this->categries);

        $this->template_dir = 'channel';
        
        $this->assign('navmodel', 'article-' . $this->channel['name'] .'-'. $this->category['name']);

        if($this->channel['use_template']>0){
            $this->template_dir = empty($this->channel['template_dir'])?$this->channel['name']:$this->channel['template_dir'];
        }
        if(!empty($this->categoryTree)) {
            
            $subTempDir = '';
            foreach ($this->categoryTree as $cate){
                if($cate['pagesize']>0){
                    $this->pagesize=intval($cate['pagesize']);
                }
                if($cate['id'] != $this->channel['id'] && $cate['use_template'] > 0){
                    $subTempDir = '/'.empty($cate['template_dir'])?$cate['name']:$cate['template_dir'];
                }
            }
            if(!empty($subTempDir)){
                $this->template_dir .= $subTempDir;
            }
        }
    }
}