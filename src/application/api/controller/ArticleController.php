<?php

namespace app\api\controller;

use app\common\facade\CategoryFacade;
use app\common\facade\MemberFavouriteFacade;
use app\common\model\ArticleCommentModel;
use app\common\model\ArticleModel;
use app\common\validate\ArticleCommentValidate;
use shirne\third\Aliyun;
use think\Db;
use think\response\Json;

/**
 * 文章操作接口
 * Class ArticleController
 * @package app\api\Controller
 */
class ArticleController extends BaseController
{
    /**
     * 获取全部文章分类
     * 格式
     *   0 => 顶级类列表
     *   id => 子类列表
     *   ...
     * @return Json 
     */
    public function get_all_cates(){
        return $this->response(CategoryFacade::getTreedCategory());
    }

    /**
     * 获取指定id的子类，可携带指定数量和筛选条件的文章
     * @param int $pid 
     * @param int $list_count 
     * @param array $filters 
     * @return Json 
     */
    public function get_cates($pid=0, $list_count=0, $filters=[]){
        if($pid != '0' || preg_match('/^[a-zA-Z]\w+$/',$pid)){
            $current=CategoryFacade::findCategory($pid);
            if(empty($current)){
                return $this->response([]);
            }
            $pid=$current['id'];
        }
        $cates = CategoryFacade::getSubCategory($pid);
        if($list_count > 0){
            $article = ArticleModel::getInstance();
            $filters['limit']=$list_count;
            if(!isset($filters['recursive'])){
                $filters['recursive']=1;
            }
            foreach($cates as &$cate){
                $filters['category']=$cate['id'];
                $cate['articles']=$article->tagList($filters);
            }
            unset($cate);
        }
        return $this->response($cates);
    }

    /**
     * 获取文章列表
     * @param string $cate 指定所属的分类，默认包含子类
     * @param string $order 指定排序
     * @param string $keyword 指定关键字
     * @param int $page 指定分页
     * @param string $type 指定文章类型
     * @param int $pagesize 指定获取数量，分页时为每页大小
     * @return Json 
     */
    public function get_list($cate='',$order='',$keyword='',$page=1,$type='', $pagesize=10){
    
        $condition=[];
        if($cate){
            $condition['category']=$cate;
            $condition['recursive']=1;
        }
        if(!empty($order)){
            $condition['order']=$order;
        }
        if(!empty($keyword)){
            $condition['keyword']=$keyword;
        }
        if($type !== ''){
            $condition['type']=$type;
        }
        $condition['page']=$page;
        $condition['pagesize']=$pagesize;
    
        $lists = ArticleModel::getInstance()->tagList($condition, true);
        $category=CategoryFacade::findCategory($cate);
        
        return $this->response([
            'lists'=>$lists->items(),
            'category'=>$category?:new \stdClass(),
            'page'=>$lists->currentPage(),
            'total'=>$lists->total(),
            'total_page'=>$lists->lastPage(),
        ]);
    }

    /**
     * 获取指定文章详情
     * @param mixed $id 
     * @return Json 
     */
    public function view($id){
        $id=intval($id);
        $article = ArticleModel::get($id);
        if(empty($article)){
            $this->error('文章不存在',0);
        }
        $article->setInc('views',1);
        $article['views']+=$article['v_views'];
        $article['digg']+=$article['v_digg'];
        $images=Db::name('ArticleImages')->where('article_id',$article['id'])->select();

        $digg=false;
        $isFavourite=0;
        if($this->isLogin) {
            $digg = Db::name('articleDigg')->where('article_id', $id)
                ->where('member_id', $this->user['id'])
                ->count();
            $isFavourite=MemberFavouriteFacade::isFavourite($this->user['id'],'article',$id);
        }
        return $this->response([
            'article'=>$article,
            'url'=>url('index/article/index',['id'=>$article['id']],true,true),
            'images'=>$images,
            'digged'=>$digg?1:0,
            'is_favourite'=>$isFavourite?1:0
        ]);
    }

    /**
     * 点赞指定文章，需要登录才能操作
     * @param mixed $id 
     * @param string $type 
     * @return Json 
     */
    public function digg($id,$type='up'){
        $id=intval($id);
        $article = ArticleModel::get($id);
        if(empty($article)){
            $this->error('文章不存在',0);
        }
        if(!$this->isLogin){
            $this->error('请先登录',ERROR_NEED_LOGIN);
        }

        $digg=Db::name('articleDigg')->where('article_id',$id)
            ->where('member_id',$this->user['id'])
            ->find();
        if(empty($digg)){
            if($type=='up') {
                $article->setInc('digg', 1);
                Db::name('articleDigg')->insert([
                    'article_id'=>$id,
                    'member_id'=>$this->user['id'],
                    'create_time'=>time(),
                    'device'=>'',
                    'ip'=>$this->request->ip()
                ]);
            }else{
                $this->error('您没对这篇文章点过赞',0);
            }
        }else{
            if($type!=='up'){
                $article->setDec('digg',1);
                Db::name('articleDigg')->where('id',$digg['id'])->delete();
            }else{
                $this->error('您已经点过赞啦',0);
            }
        }

        return $this->response([
            'digg'=>$article['digg']+$article['v_digg']
        ]);
    }

    /**
     * 获取指定文章的评论，可分页
     * @param int $id 
     * @param int $pagesize
     * @param int $page
     * @return Json 
     */
    public function comments($id, $pagesize = 10){
        $model = Db::view('articleComment','*')
        ->view('member',['username','realname','avatar'],'member.id=articleComment.member_id','LEFT')
        ->where('article_id',$id);
        if($this->isLogin){
            $model->where(function($query){
                return $query->where('articleComment.status',1)
                ->whereOr('articleComment.member_id',$this->user['id']);
            });
        }else{
            $model->where('articleComment.status',1);
        }
        $comments=$model->order('articleComment.create_time desc')->paginate($pagesize);

        return $this->response([
            'lists'=>$comments->items(),
            'page'=>$comments->currentPage(),
            'total'=>$comments->total(),
            'total_page'=>$comments->lastPage(),
        ]);
    }

    /**
     * 提交评论 可指定要回复的评论
     * @param int $id 
     * @param int $reply_id 
     * @return void 
     */
    public function do_comment($id, $reply_id = 0){
        $this->check_submit_rate();
        $article = Db::name('article')->find($id);
        if(empty($article)){
            $this->error(lang('Arguments error!'));
        }
        
        $data=$this->request->only('email,is_anonymous,content','put');
        if($this->config['anonymous_comment']==0 && !$this->isLogin){
            $this->error('请登录后评论', ERROR_NEED_LOGIN);
        }
        $data['article_id']=$id;
        $validate=new ArticleCommentValidate();
        if(!$validate->check($data)){
            $this->error($validate->getError(),0);
        }else{
            $data['member_id']=$this->isLogin?$this->user['id']:0;
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
            if(!empty($reply_id)){
                $reply=Db::name('ArticleComment')->find($data['reply_id']);
                if(empty($reply)){
                    $this->error('回复的评论不存在');
                }
                $data['reply_id'] = $reply_id;
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
                $this->success('评论成功');
            }else{
                $this->error('评论失败',0);
            }
        }
    }
}