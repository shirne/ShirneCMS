<?php

namespace app\common\taglib;


use app\common\core\BaseTabLib;

class Goods extends BaseTabLib
{
    protected $tags =[
        'list'=>['attr'=>'var,category,type,ids,limit,image,recursive','close'=>0],
        'relation'=>['attr'=>'var,category,id,limit','close'=>0],
        'cates'=>['attr'=>'var,pid,limit','close'=>0],
        'cate'=>['attr'=>'var,name','close'=>0],
    ];

    public function tagList($tag){
        $var  = isset($tag['var']) ? $tag['var'] : 'goods_list';
        $recursive =isset($tag['recursive']) ? $tag['recursive'] : 'false';
        $category=isset($tag['category']) ? $this->parseArg($tag['category']) : '';
        if(is_string($category) && strpos($category,"'")===0){
            $category="\\app\\common\\facade\\GoodsCategoryFacade::getCategoryId(".$category.")";
        }

        $parseStr='<?php ';

        $parseStr.='$'.$var.'=\think\Db::view("Goods","*")';
        $parseStr .= '->view("GoodsCategory",["title"=>"category_title","name"=>"category_name","short"=>"category_short","icon"=>"category_icon","image"=>"category_image"],"Goods.cate_id=GoodsCategory.id","LEFT")';
        $parseStr .= '->where("Goods.status",1)';
        if(!empty($category)){
            if($recursive=='true'){
                $parseStr .= '->whereIn("Goods.cate_id", \app\common\facade\GoodsCategoryFacade::getSubCateIds(' . $category . '))';
            }else {
                $parseStr .= '->where("Goods.cate_id",' . $category . ')';
            }
        }
        if(!empty($tag['type'])){
            $parseStr .= '->where("Goods.type",'.intval($tag['type']).')';
        }
        if(!empty($tag['image'])){
            $parseStr .= '->where("Goods.image","<>","")';
        }
        if(empty($tag['limit'])){
            $tag['limit']=10;
        }
        $parseStr .= '->limit('.intval($tag['limit']).')';
        $parseStr .= '->order("Goods.sort DESC,Goods.id DESC")';
        $parseStr .= '->select();';

        $parseStr .= ' ?>';
        return $parseStr;
    }
    public function tagRelation($tag){
        $var  = isset($tag['var']) ? $tag['var'] : 'relations';
        $category=isset($tag['category']) ? $tag['category'] : '';
        $id=isset($tag['id']) ? $tag['id'] : 0;
        if(preg_match('/^[a-zA-Z]\w*$/',$category)){
            $category="\\app\\common\\facade\\GoodsCategoryFacade::getCategoryId('".$category."')";
        }

        $parseStr='<?php ';

        $parseStr.='$'.$var.'=\think\Db::view("Goods","*")';
        $parseStr .= '->view("GoodsCategory",["title"=>"category_title","name"=>"category_name","short"=>"category_short","icon"=>"category_icon","image"=>"category_image"],"Goods.cate_id=GoodsCategory.id","LEFT")';
        $parseStr .= '->where("Goods.status",1)';
        $parseStr .= '->where("Goods.id", "<>", ' . $id . ')';
        if(!empty($category)){
            $parseStr .= '->whereIn("Goods.cate_id", \app\common\facade\GoodsCategoryFacade::getSubCateIds(' . $category . '))';
        }
        if(empty($tag['limit'])){
            $tag['limit']=10;
        }
        $parseStr .= '->limit('.intval($tag['limit']).')';
        $parseStr .= '->order("Goods.sale DESC,Goods.id DESC")';
        $parseStr .= '->select();';

        $parseStr .= ' ?>';
        return $parseStr;
    }

    public function tagCates($tag){
        $var  = isset($tag['var']) ? $tag['var'] : 'cates_list';
        $pid = isset($tag['pid']) ? $this->parseArg($tag['pid']) : 0;

        $parseStr='<?php ';

        $parseStr.='$'.$var.'=\think\Db::name("GoodsCategory")';
        if(!empty($tag['pid'])){
            $parseStr .= "->where('pid','.$pid.')";
        }
        $parseStr .= '->order("sort ASC, id ASC")';
        if(!empty($tag['limit'])){
            $parseStr .= '->limit('.intval($tag['limit']).')';
        }
        $parseStr .= '->select();';

        $parseStr .= ' ?>';
        return $parseStr;
    }
    public function tagCate($tag){
        $var  = isset($tag['var']) ? $tag['var'] : 'cate';
        $name = isset($tag['name']) ? $this->parseArg($tag['name']) : 0;

        $parseStr='<?php ';

        $parseStr.='$'.$var.'=\app\common\facade\GoodsCategoryFacade::findCategory('.$name.');';

        $parseStr .= ' ?>';
        return $parseStr;
    }
}