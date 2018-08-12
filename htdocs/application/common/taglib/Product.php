<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/5/13
 * Time: 8:43
 */

namespace app\common\taglib;


class Product extends BaseTabLib
{
    protected $tags =[
        'list'=>['attr'=>'var,category,type,limit,image,recursive','close'=>0],
        'relation'=>['attr'=>'var,category,id','close'=>0],
        'cates'=>['attr'=>'var,pid','close'=>0],
        'cate'=>['attr'=>'var,name','close'=>0],
    ];

    public function tagList($tag){
        $var  = isset($tag['var']) ? $tag['var'] : 'product_list';
        $recursive =isset($tag['recursive']) ? $tag['recursive'] : 'false';
        $category=isset($tag['category']) ? $this->parseArg($tag['category']) : '';
        if(is_string($category) && strpos($category,"'")===0){
            $category="\\app\\common\\facade\\ProductCategoryFacade::getCategoryId(".$category.")";
        }

        $parseStr='<?php ';

        $parseStr.='$'.$var.'=\think\Db::view("Product","*")';
        $parseStr .= '->view("ProductCategory",["title"=>"category_title","name"=>"category_name","short"=>"category_short","icon"=>"category_icon","image"=>"category_image"],"Product.cate_id=ProductCategory.id","LEFT")';
        if(!empty($category)){
            if($recursive=='true'){
                $parseStr .= '->where("Product.cate_id", "IN", \app\common\facade\ProductCategoryFacade::getSubCateIds(' . $category . '))';
            }else {
                $parseStr .= '->where("Product.cate_id",' . $category . ')';
            }
        }
        if(!empty($tag['type'])){
            $parseStr .= '->where("Product.type",'.intval($tag['type']).')';
        }
        if(!empty($tag['image'])){
            $parseStr .= '->where("Product.image","<>","")';
        }
        if(empty($tag['limit'])){
            $tag['limit']=10;
        }
        $parseStr .= '->limit('.intval($tag['limit']).')';
        $parseStr .= '->select();';

        $parseStr .= ' ?>';
        return $parseStr;
    }
    public function tagRelation($tag){
        $var  = isset($tag['var']) ? $tag['var'] : 'relations';
        $category=isset($tag['category']) ? $tag['category'] : '';
        $id=isset($tag['id']) ? $tag['id'] : 0;
        if(preg_match('/^[a-zA-Z]\w*$/',$category)){
            $category="\\app\\common\\facade\\ProductCategoryFacade::getCategoryId('".$category."')";
        }

        $parseStr='<?php ';

        $parseStr.='$'.$var.'=\think\Db::view("Product","*")';
        $parseStr .= '->view("ProductCategory",["title"=>"category_title","name"=>"category_name","short"=>"category_short","icon"=>"category_icon","image"=>"category_image"],"Product.cate_id=ProductCategory.id","LEFT")';
        $parseStr .= '->where("Product.id", "NEQ", ' . $id . ')';
        if(!empty($category)){
            $parseStr .= '->where("Product.cate_id", "IN", \app\common\facade\ProductCategoryFacade::getSubCateIds(' . $category . '))';
        }
        $parseStr .= '->order("Product.sale DESC,Product.id DESC")';
        $parseStr .= '->select();';

        $parseStr .= ' ?>';
        return $parseStr;
    }

    public function tagCates($tag){
        $var  = isset($tag['var']) ? $tag['var'] : 'cates_list';
        $pid = isset($tag['pid']) ? $this->parseArg($tag['pid']) : 0;

        $parseStr='<?php ';

        $parseStr.='$'.$var.'=\think\Db::name("ProductCategory")';
        if(!empty($tag['pid'])){
            $parseStr .= "->where('pid','.$pid.')";
        }
        $parseStr .= '->order("sort ASC, id ASC")';
        $parseStr .= '->select();';

        $parseStr .= ' ?>';
        return $parseStr;
    }
    public function tagCate($tag){
        $var  = isset($tag['var']) ? $tag['var'] : 'cate';
        $name = isset($tag['name']) ? $this->parseArg($tag['name']) : 0;

        $parseStr='<?php ';

        $parseStr.='$'.$var.'=\app\common\facade\ProductCategoryFacade::findCategory('.$name.');';

        $parseStr .= ' ?>';
        return $parseStr;
    }
}