<?php

namespace app\common\model;

use app\common\facade\CategoryFacade;
use think\Db;

/**
 * Class ContentModel
 * @package app\common\model
 */
class ContentModel extends BaseModel
{
    protected $model;
    protected $cateModel;
    protected $defaultOrder='id DESC';

    /**
     * @var $cateFacade CategoryModel
     */
    protected $cateFacade;
    protected function tagBase()
    {
        $this->model=ucfirst($this->name);
        $this->cateModel=($this->model=='Article'?'':$this->model).'Category';
        return Db::view($this->model,'*')
            ->view($this->cateModel,
                ["title"=>"category_title","name"=>"category_name","short"=>"category_short","icon"=>"category_icon","image"=>"category_image"],
                $this->model.".cate_id=".$this->cateModel.".id",
                "LEFT"
            )
            ->where($this->model.".status",1);
    }

    public function tagList($attrs)
    {
        $model=$this->tagBase();
        if(!empty($attrs['category'])){
            $cate_id=$attrs['category'];
            if(!is_int($cate_id)){
                $cate_id=$this->cateFacade->getCategoryId($cate_id);
            }
            if(isset($attrs['recursive']) && $attrs['recursive']){
                $model->where($this->model.".cate_id", "IN", $this->cateFacade->getSubCateIds($cate_id));
            }else{
                $model->where($this->model.".cate_id",$cate_id);
            }
        }
        if(!empty($attrs['brand'])){
            if(strpos($attrs['brand'],',')>0){
                $model->whereIn($this->model . ".brand_id", idArr($attrs['brand']));
            }else {
                $model->where($this->model . ".brand_id", intval($attrs['brand']));
            }
        }
        if(!empty($attrs['type'])){
            $model->where($this->model.".type",$attrs['type']);
        }
        if(!empty($attrs['cover'])){
            $model->where($this->model.".cover","<>","");
        }
        if(!empty($attrs['image'])){
            $model->where($this->model.".image","<>","");
        }
        if(empty($attrs['limit'])){
            $attrs['limit']=10;
        }
        $model->limit($attrs['limit']);

        if(empty($attrs['order'])){
            $attrs['order']=$this->defaultOrder;
        }
        if(strpos($attrs['order'],'.')===false){
            $attrs['order'] = $this->model.'.'.$attrs['order'];
        }
        $model->order($attrs['order']);

        return $model->select();
    }

    public function tagRelation($attrs)
    {
        $model=$this->tagBase();
        if(!empty($attrs['category'])){
            $cate_id=$attrs['category'];
            if(!is_int($cate_id)){
                $cate_id=$this->cateFacade->getCategoryId($cate_id);
            }

            //默认递归分类
            if(isset($attrs['recursive']) && $attrs['recursive']===false){
                $model->where($this->model.".cate_id",$cate_id);
            }else{
                $model->where($this->model.".cate_id", "IN", $this->cateFacade->getSubCateIds($cate_id));
            }
        }
        if(!empty($attrs['id'])){
            $model->where($this->model.".id", "NEQ",  $attrs['id'] );
        }
        if(empty($attrs['limit'])){
            $attrs['limit']=10;
        }
        $model->limit($attrs['limit']);
        if(empty($attrs['order'])){
            $attrs['order']=$this->model.'.'.$this->defaultOrder;
        }
        if(strpos($attrs['order'],'.')===false){
            $attrs['order'] = $this->model.'.'.$attrs['order'];
        }
        $model->order($attrs['order']);

        return $model->select();
    }

    public function tagPrev($attrs)
    {
        $model=$this->tagBase();
        if(!empty($attrs['category'])){
            $cate_id=$attrs['category'];
            if(!is_int($cate_id)){
                $cate_id=$this->cateFacade->getCategoryId($cate_id);
            }

            //默认递归分类
            if(isset($attrs['recursive']) && $attrs['recursive']===false){
                $model->where($this->model.".cate_id",$cate_id);
            }else{
                $model->where($this->model.".cate_id", "IN", $this->cateFacade->getSubCateIds($cate_id));
            }
        }
        if(!empty($attrs['id'])){
            $model->where($this->model.".id", "LT",  $attrs['id'] );
        }

        $model->order($this->model.'.'.$this->getPk().' DESC');

        return $model->find();
    }

    public function tagNext($attrs)
    {
        $model=$this->tagBase();
        if(!empty($attrs['category'])){
            $cate_id=$attrs['category'];
            if(!is_int($cate_id)){
                $cate_id=$this->cateFacade->getCategoryId($cate_id);
            }

            //默认递归分类
            if(isset($attrs['recursive']) && $attrs['recursive']===false){
                $model->where($this->model.".cate_id",$cate_id);
            }else{
                $model->where($this->model.".cate_id", "IN", $this->cateFacade->getSubCateIds($cate_id));
            }
        }
        if(!empty($attrs['id'])){
            $model->where($this->model.".id", "GT",  $attrs['id'] );
        }

        $model->order($this->model.'.'.$this->getPk().' ASC');

        return $model->find();
    }
}