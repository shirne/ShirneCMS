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
        if(!empty($this->category)){
            $this->seo($this->category['title']);
        }else{
            $this->seo("新闻中心");
        }

        $where=array();
        $cids=array();
        foreach ($this->categotyTree as $cate){
            $cids[]=$cate['id'];
        }
        if(!empty($cids)){
            $where['article.cate_id']=array('in',$cids);
        }
        $model=Db::view('article','*')
        ->view('category',['name'=>'category_name','title'=>'category_title'],'article.cate_id=category.id','LEFT')
        ->view('manager',['username'],'manager.id=article.user_id','LEFT')
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
        $categories=cache('allcate');
        if(empty($categories)) {
            $categories = Db::name('category')->order('pid ASC,sort ASC,id ASC')->select();
            $categories = array_combine(array_column($categories,'id'),$categories);
            cache('allcate',$categories);
        }

        $this->category=array();
        $this->categotyTree=array();
        if(!empty($name)){
            foreach ($categories as $cate){
                if($cate['id']==$name || $cate['name']==$name){
                    $this->category=$cate;
                    break;
                }
            }
            if(!empty($this->category)) {
                $this->categotyTree = array($this->category);
                $pid = $this->category['pid'];
                while ($pid > 0) {
                    if (!isset($categories[$pid])) break;
                    array_unshift($this->categotyTree, $categories[$pid]);
                    $pid = $this->categotyTree[0]['pid'];
                }
            }
        }
        $this->categries=array('0'=>[]);
        foreach ($categories as $cate){
            $this->categries[$cate['pid']][$cate['id']]=$cate;
        }

        $this->assign('category',$this->category);
        $this->assign('categotyTree',$this->categotyTree);
        $this->assign('categories',$this->categries);
    }
}
