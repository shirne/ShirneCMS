<?php

namespace app\index\controller;

use \think\Db;
/**
 * 发布文章必须登录
 */
class PostController extends BaseController{

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
            $where['post.cate_id']=array('in',$cids);
        }
        $model=Db::view('post','*')
        ->view('category',['name'=>'category_name','title'=>'category_title'],'post.cate_id=category.id','LEFT')
        ->view('manager',['username'],'LEFT')
        ->paginate(10);

        $this->assign('lists', $model);
        $this->assign('page',$model->render());
        return $this->fetch();
    }

    public function view($id){
        $post = Db::name('post')->find($id);
        $this->seo($post['title']);
        $this->category($post['cate_id']);

        $this->assign('post', $post);
        return $this->fetch();
    }
    public function notice($id){
        $post = Db::name('notice')->find($id);
        $this->seo($post['title']);
        $this->category();

        $this->assign('post', $post);
        return $this->fetch();
    }

    private function category($name=''){
        $categories=cache('allcate');
        if(empty($categories)) {
            $cates = Db::name('category')->select();
            foreach ($cates as $cate) {
                $categories[$cate['id']] = $cate;
            }
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
        $this->categries=array();
        foreach ($categories as $cate){
            $this->categries[$cate['pid']][$cate['id']]=$cate;
        }

        $this->assign('category',$this->category);
        $this->assign('categotyTree',$this->categotyTree);
        $this->assign('categories',$this->categries);
    }
}
