<?php

namespace app\index\controller;


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
        $model=D('PostView');

        $count  = $model->where($where)->count();// 查询满足要求的总记录数
        $Page = new \Extend\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();// 分页显示输出
        $lists = $model->where($where)->limit($Page->firstRow.','.$Page->listRows)->order('post.id DESC')->select();

        $this->assign('lists', $lists);
        $this->assign('page',$show);
        $this->display();
    }

    public function view($id){
        $post = M('post')->find($id);
        $this->seo($post['title']);
        $this->category($post['cate_id']);

        $this->assign('post', $post);
        $this->display();
    }
    public function notice($id){
        $post = M('notice')->find($id);
        $this->seo($post['title']);
        $this->category();

        $this->assign('post', $post);
        $this->display();
    }

    private function category($name=''){
        $categories=S('allcate');
        if(empty($categories)) {
            $cates = M('category')->select();
            foreach ($cates as $cate) {
                $categories[$cate['id']] = $cate;
            }
            S('allcate',$categories);
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
