<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/5/13
 * Time: 8:43
 */

namespace app\common\taglib;


use think\template\TagLib;

class Product extends TagLib
{
    protected $tags =[
        'list'=>['attr'=>'var,category,type,limit,image,recursive','close'=>0],
        'cates'=>['attr'=>'var,pid','close'=>0],
        'cate'=>['attr'=>'var,name','close'=>0],
    ];

    public function tagList($tag){
        $var  = isset($tag['var']) ? $tag['var'] : 'product_list';
        $recursive =isset($tag['recursive']) ? $tag['recursive'] : 'false';
        $category=isset($tag['category']) ? $tag['category'] : '';
        if(preg_match('/^[a-zA-Z]\w*$/',$category)){
            $category="\\app\\common\\facade\\ProductCategoryModel::getCategoryId('".$category."')";
        }else{
            $category=intval($category);
        }

        $parseStr='<?php ';

        $parseStr.='$'.$var.'=\think\Db::view("Product","*")';
        $parseStr .= '->view("ProductCategory",["title"=>"category_title","name"=>"category_name","short"=>"category_short","icon"=>"category_icon","image"=>"category_image"],"Product.cate_id=ProductCategory.id","LEFT")';
        if(!empty($category)){
            if($recursive=='true'){
                $parseStr .= '->where("Product.cate_id", "IN", \app\common\facade\ProductCategoryModel::getSubCateIds(' . $category . '))';
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

    public function tagCates($tag){
        $var  = isset($tag['var']) ? $tag['var'] : 'cates_list';
        $pid = isset($tag['pid']) ? intval($tag['pid']) : 0;

        $parseStr='<?php ';

        $parseStr.='$'.$var.'=\think\Db::name("ProductCategory")';
        if(!empty($tag['category'])){
            $parseStr .= '->where("pid",'.$pid.')';
        }
        $parseStr .= '->order("sort ASC, id ASC")';
        $parseStr .= '->select();';

        $parseStr .= ' ?>';
        return $parseStr;
    }
    public function tagCate($tag){
        $var  = isset($tag['var']) ? $tag['var'] : 'cate';
        $name = isset($tag['name']) ? intval($tag['name']) : 0;
        if(preg_match('/^[a-zA-Z]\w*$/',$name)){
            $name="'".$name."'";
        }else{
            $name=intval($name);
        }

        $parseStr='<?php ';

        $parseStr.='$'.$var.'=\app\common\facade\ProductCategoryModel::findCategory('.$name.');';

        $parseStr .= ' ?>';
        return $parseStr;
    }
}