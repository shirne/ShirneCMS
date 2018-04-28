<?php

namespace app\admin\controller;

/**
 * 分类管理
 */
class CategoryController extends BaseController
{
    /**
     * 分类列表
     */
    public function index($key="")
    {
        $model = M('category');
        $where=array();
        if(!empty($key)){
            $where['title|name'] = array('like',"%$key%");
        } 
        
        $category = $model->where($where)->order('pid ASC,`sort` ASC,id ASC')->select();
        $this->assign('model',getSortedCategory($category));
        $this->display();   
    }

    /**
     * 添加分类
     */
    public function edit($id=0)
    {
        if (IS_POST) {
            //如果用户提交数据
            $model = D("Category");
            if (!$model->create()) {
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                $this->error($model->getError());
                exit();
            } else {
                $iconupload=$this->upload('category','upload_icon',true);
                if(!empty($iconupload))$model->icon=$iconupload['url'];
                $uploaded=$this->upload('category','upload_image',true);
                if(!empty($uploaded))$model->image=$uploaded['url'];
                if ($model->add()) {
                    $this->success(($id>0?'保存':'添加')."成功", U('category/index'));
                } else {
                    $this->error(($id>0?'保存':'添加')."失败");
                }
            }
        }else{
            if($id>0) {
                $model = M('category')->find($id);
            }else{
                $model=array('sort'=>99);
            }
            $cate = getSortedCategory(M('category')->order('pid ASC,`sort` ASC')->select());

            $this->assign('cate',$cate);
            $this->assign('model',$model);
            $this->assign('id',$id);
            $this->display();
        }
    }

    /**
     * 删除分类
     */
    public function delete($id)
    {
    		$id = intval($id);
        $model = M('category');
        //查询属于这个分类的文章
        $posts = M('post')->where("cate_id= %d",$id)->select();
        if($posts){
            $this->error("禁止删除含有文章的分类");
        }
        //禁止删除含有子分类的分类
        $hasChild = $model->where("pid= %d",$id)->select();
        if($hasChild){
            $this->error("禁止删除含有子分类的分类");
        }
        //验证通过
        $result = $model->delete($id);
        if($result){
            $this->success("分类删除成功", U('category/index'));
        }else{
            $this->error("分类删除失败");
        }
    }
}
