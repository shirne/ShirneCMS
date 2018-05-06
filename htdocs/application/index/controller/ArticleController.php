<?php

namespace app\index\controller;

use \think\Db;
/**
 * 发布文章必须登录
 */
class ArticleController extends BaseController{

    protected $categries;
    protected $category;
    protected $categotyTree;

    public function index($name=""){
        $this->category($name);

        $where=array();
        if(!empty($this->category)){
            $this->seo($this->category['title']);
            $where[]=array('article.cate_id','in',getSubCateids($this->category['id']));
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
        return $this->fetch();
    }
    public function notice($id){
        $article = Db::name('notice')->find($id);
        $this->seo($article['title']);
        $this->category();

        $this->assign('article', $article);
        return $this->fetch();
    }

    private function category($name=''){
        $categories=getArticleCategories();

        $this->category=findCategory($categories,$name);
        $this->categotyTree=getArticleCategoryTree($name);

        $this->categries=getTreedCategory();

        $this->assign('category',$this->category);
        $this->assign('categotyTree',$this->categotyTree);
        $this->assign('categories',$this->categries);
    }
}