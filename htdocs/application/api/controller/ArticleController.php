<?php

namespace app\api\Controller;

use app\common\facade\CategoryFacade;
use app\common\model\ArticleCommentModel;
use app\common\model\ArticleModel;
use app\common\validate\ArticleCommentValidate;
use think\Db;

/**
 * 文章操作接口
 * Class ArticleController
 * @package app\api\Controller
 */
class ArticleController extends BaseController
{
    public function get_all_cates(){
        $this->response(CategoryFacade::getTreedCategory());
    }

    public function get_cates($pid=0){
        if($pid!=0 && preg_match('/^[a-zA-Z]\w+/',$pid)){
            $current=CategoryFacade::findCategory($pid);
            if(empty($current)){
                $this->response([]);
            }
            $pid=$current['id'];
        }
        $this->response(CategoryFacade::getSubCategory($pid));
    }

    public function get_list($cate=''){
        $model=Db::view('article','*')
            ->view('category',['name'=>'category_name','title'=>'category_title'],'article.cate_id=category.id','LEFT')
            ->view('manager',['username'],'manager.id=article.user_id','LEFT');

        $model->where('status',1);
        if($cate){
            $category=CategoryFacade::findCategory($cate);
            $model->whereIn('cate_id',CategoryFacade::getSubCateIds($category['id']));
        }
        $lists = $model->paginate(10);
        $lists->each(function($item){
            if(!empty($item['prop_data'])){
                $item['prop_data']=json_decode($item['prop_data'],true);
            }
            $item['prop_data']=[];
            return $item;
        });
        $this->response([
            'lists'=>$lists->items(),
            'page'=>$lists->currentPage(),
            'count'=>$lists->total(),
            'total_page'=>$lists->lastPage(),
        ]);
    }

    public function view($id){
        $id=intval($id);
        $article = ArticleModel::get($id);
        if(empty($article)){
            $this->response('文章不存在',0);
        }
        $article->setInc('views',1);
        $images=Db::name('ArticleImages')->where('article_id',$article['id'])->select();

        $this->response([
            'article'=>$article,
            'images'=>$images
        ]);
    }

    public function comments($id){
        $comments=Db::view('articleComment','*')
            ->view('member',['username','realname'],'member.id=articleComment.member_id','LEFT')
            ->where('article_id',$id)->paginate(10);

        $this->response([
            'lists'=>$comments->items(),
            'page'=>$comments->currentPage(),
            'count'=>$comments->total(),
            'total_page'=>$comments->lastPage(),
        ]);
    }

    public function do_comment(){
        $data=$this->request->only('article_id,email,is_anonymous,content,reply_id','POST');
        $validate=new ArticleCommentValidate();
        if(!$validate->check($data)){
            $this->response($validate->getError(),0);
        }else{
            $data['member_id']=$this->isLogin?$this->user['id']:0;
            if(!empty($data['member_id'])){
                $data['email']=$this->user['email'];
            }else{
                if(empty($data['email'])){
                    $this->response('请填写邮箱',0);
                }
            }
            if(!empty($data['reply_id'])){
                $reply=Db::name('ArticleComment')->find($data['reply_id']);
                if(empty($reply)){
                    $this->response('回复的评论不存在',0);
                }
                $data['group_id']=empty($reply['group_id'])?$reply['id']:$reply['group_id'];
            }
            $model=ArticleCommentModel::create($data);
            if($model['id']){
                $this->response('评论成功');
            }else{
                $this->response('评论失败',0);
            }
        }
    }
}