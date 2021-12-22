<?php

namespace app\common\core;

use think\Db;
use think\Model;
use think\Paginator;

/**
 * Class ContentModel
 * @package app\common\model
 */
class ContentModel extends BaseModel
{
    protected $model;
    protected $cateModel;
    protected $searchFields='title';
    protected $hiddenFields='content';
    private $transedSearchFields='';
    protected $defaultOrder='id DESC';

    public function setTypeAttr($value)
    {
        if(is_array($value)){
            $result=0;
            foreach($value as $val){
                $result = $result | $val;
            }
            return $result;
        }
        return intval($value);
    }

    /**
     * @var $cateFacade \app\common\model\CategoryModel
     */
    protected $cateFacade;

    protected function tagBase($hidden=null)
    {
        $this->model=ucfirst($this->name);
        if(empty($this->cateModel)){
            $this->cateModel=($this->model=='Article'?'':$this->model).'Category';
        }
        if(is_null($hidden )){
            $hidden = $this->hiddenFields;
        }
        $fields = '*';
        if(!empty($hidden)){
            $fields = $this->getTableFields();
            if(!empty($fields)){
                $hiddens = explode(',',$hidden);
                $fields = array_diff($fields,$hiddens);
            }
        }
        $model = Db::view($this->model,$fields)
            ->view($this->cateModel,
                ["title"=>"category_title","name"=>"category_name","short"=>"category_short","icon"=>"category_icon","image"=>"category_image"],
                $this->model.".cate_id=".$this->cateModel.".id",
                "LEFT"
            );
        
        return $this->tagBaseView($model)->where($this->model.".status",1);
    }
    protected function tagBaseView($model){
        return $model;
    }
    
    protected function analysisType($list, $islist=true){
        if($this->type){
            if($islist) {
                if($list instanceof Paginator){
                    $list->each(function($item){
                        return $this->analysisType($item, false);
                    });
                }else {
                    foreach ($list as $k => $item) {
                        $list[$k] = $this->analysisType($item, false);
                    }
                }
            }else{
                foreach ($this->type as $f => $type) {
                    $list[$f] = $this->readTransform($list[$f],$type);
                }
            }
        }
        return $list;
    }
    
    protected function filterOrder($order){
        if(strpos($order,'(')!==false){
            $order = str_replace(str_split('()+-/*@#%!`~'),'',$order);
        }
        if(strpos($order,'__RAND__')!==false){
            $order = str_replace('__RAND__','rand()',$order);
        }
        return $order;
    }
    
    protected function getSearchFields(){
        if($this->transedSearchFields)return $this->transedSearchFields;
        $fields = explode('|',$this->searchFields);
        foreach ($fields as $k=>$field){
            if(strpos($field,'.')===false){
                $fields[$k] = $this->model.'.'.$field;
            }
        }
        $this->transedSearchFields = implode('|',$fields);
        return $this->transedSearchFields;
    }
    

    public function isType($flag, $type = -1)
    {
        if($type < 0){
            if($this->isEmpty()){
                throw new \Exception('Need argument $type');
            }
            $type = $this['type'];
        }
        return ($type & $flag) === $flag;
    }
    
    /**
     * 重写,标签列表之后的数据处理
     * @param $item array|Paginator
     * @param $attrs array
     * @return mixed
     */
    protected function afterTagList($lists,$attrs){
        return $lists;
    }

    protected function appendTagData($lists, $key, $vals=[], $idKey='id')
    {
        $datas = [];
        if(is_array($key)){
            if(empty($key)){
                return $lists;
            }
            $datas=$key;
            if(is_string($vals) && !empty($vals)){
                $idKey=$vals;
            }
        }else{
            $datas[$key]=$vals;
        }

        if($lists instanceof Paginator){
            $lists->each(function ($item)use($datas,$idKey){
                foreach($datas as $key=>$values){
                    if(isset($values[$item[$idKey]])){
                        $item[$key]=$values[$item[$idKey]];
                    }else{
                        $item[$key]=[];
                    }
                }
                
                return $this->afterTagItem($item);
            });
        }else{
            foreach ($lists as &$item){
                foreach($datas as $key=>$values){
                    if(isset($values[$item[$idKey]])){
                        $item[$key]=$values[$item[$idKey]];
                    }else{
                        $item[$key]=[];
                    }
                }
                $item=$this->afterTagItem($item);
            }
            unset($item);
        }
        return $lists;
    }
    
    /**
     * 重写，标签单项之后的数据处理
     * @param $item array|Model
     * @param $attrs array
     * @return mixed
     */
    protected function afterTagItem($item,$attrs=[]){
        return $item;
    }

    /**
     * @param $attrs
     * @return array|Paginator
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function tagList($attrs, $filter=false)
    {
        $model=$this->tagBase(isset($attrs['hidden'])?$attrs['hidden']:null);
        if(!empty($attrs['category'])){
            $cate_id=$attrs['category'];
            if(!is_int($cate_id)){
                $cate_id=$this->cateFacade->getCategoryId($cate_id);
            }
            if(isset($attrs['recursive']) && $attrs['recursive']){
                $model->whereIn($this->model.".cate_id", $this->cateFacade->getSubCateIds($cate_id));
            }else{
                $model->where($this->model.".cate_id",$cate_id);
            }
        }
        $sortids=[];
        if(!empty($attrs['ids'])){
            $sortids=idArr($attrs['ids']);
            $model->whereIn($this->model . ".id",$sortids);
        }
        if(!empty($attrs['keyword'])){
            if(strpos($attrs['keyword'],'|')>0){
                $model->where($this->getSearchFields(),'REGEXP',$attrs['keyword']);
            }else{
                $model->whereLike($this->getSearchFields(),"%{$attrs['keyword']}%");
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
            $typeint=0;
            $types=array_filter(array_map('trim',explode(',',$attrs['type'])));
            foreach($types as $type){
                $typeint = $typeint|$type;
            }
            $model->where(Db::raw($this->model.".`type` & ".$typeint.' = '.$typeint));
        }
        if(!empty($attrs['cover'])){
            $model->where($this->model.".cover","<>","");
        }
        if(!empty($attrs['image'])){
            $model->where($this->model.".image","<>","");
        }

        if(empty($attrs['order'])){
            $attrs['order']=$this->model.'.'.$this->defaultOrder;
        }else {
            if($filter){
                $attrs['order']=$this->filterOrder($attrs['order']);
            }
            if (strpos($attrs['order'], '(') !== false) {
                $attrs['order'] = Db::raw($attrs['order']);
            } elseif (strpos($attrs['order'], '.') === false) {
                $attrs['order'] = $this->model . '.' . $attrs['order'];
            }
        }
        $model->order($attrs['order']);
        
        if(!empty($attrs['page'])){
            $page = max(1,intval($attrs['page']));
            $pagesize = isset($attrs['pagesize'])?intval($attrs['pagesize']):10;
            if($pagesize<1)$pagesize=1;
            $list = $model->paginate($pagesize,false,['page'=>$page]);
            
        }else {
            if (empty($attrs['limit']) && empty($attrs['ids'])) {
                $attrs['limit'] = 10;
            }
            if(!empty($attrs['limit'])){
                $model->limit($attrs['limit']);
            }
    
            $list = $model->select();
            
            if(!empty($list) && !empty($sortids) && count($sortids)>1){
                $newlist=[];
                $list = array_column($list,null,'id');
                foreach ($sortids as $id){
                    if(isset($list[$id]))$newlist[]=$list[$id];
                }
                $list=$newlist;
                unset($newlist);
            }
        }
        if(empty($list))return $list;
        
        return $this->afterTagList($this->analysisType($list),$attrs);
    }

    public function tagRelation($attrs, $filter=false)
    {
        $model=$this->tagBase($attrs['hidden']);
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
        if(empty($attrs['order'])){
            $attrs['order']=$this->model.'.'.$this->defaultOrder;
        }else {
            if($filter){
                $attrs['order']=$this->filterOrder($attrs['order']);
            }
            if (strpos($attrs['order'], '(') !== false) {
                $attrs['order'] = Db::raw($attrs['order']);
            } elseif (strpos($attrs['order'], '.') === false) {
                $attrs['order'] = $this->model . '.' . $attrs['order'];
            }
        }
        $model->order($attrs['order']);

        if(empty($attrs['limit'])){
            $attrs['limit']=10;
        }
        $model->limit($attrs['limit']);
    
        $list = $model->select();
        if(empty($list))return $list;
    
        return $this->afterTagList($this->analysisType($list),$attrs);
    }

    public function tagPrev($attrs)
    {
        $model=$this->tagBase($attrs['hidden']);
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

        $item = $model->order($this->model.'.'.$this->getPk().' DESC')->find();

        if(empty($item))return $item;
        return $this->afterTagItem($this->analysisType($item,false),$attrs);
    }

    public function tagNext($attrs)
    {
        $model=$this->tagBase($attrs['hidden']);
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

        $item = $model->order($this->model.'.'.$this->getPk().' ASC')->find();

        if(empty($item))return $item;
        return $this->afterTagItem($this->analysisType($item,false),$attrs);
    }
}