<?php

namespace app\common\taglib;

use app\common\core\BaseTabLib;

/**
 * Class Product
 * @package app\common\taglib
 */
class Product extends BaseTabLib
{
    protected $tags =[
        'list'=>['attr'=>'var,category,type,ids,limit,image,withsku,withimgs,recursive','close'=>0],
        'relation'=>['attr'=>'var,category,id,limit,withsku,withimgs','close'=>0],
        'prev'=>['attr'=>'var,category,id','close'=>0],
        'next'=>['attr'=>'var,category,id','close'=>0],
        'cates'=>['attr'=>'var,pid,limit','close'=>0],
        'cate'=>['attr'=>'var,name','close'=>0],
    ];

    public function tagList($tag){
        $var  = isset($tag['var']) ? $tag['var'] : 'product_list';

        $parseStr = '<?php ';

        $parseStr .= '$'.$var.'=\app\common\model\ProductModel::getInstance()->tagList('.$this->exportArg($tag).');';

        $parseStr .= ' ?>';
        return $parseStr;
    }
    public function tagRelation($tag){
        $var  = isset($tag['var']) ? $tag['var'] : 'relations';

        $parseStr = '<?php ';

        $parseStr .= '$'.$var.'=\app\common\model\ProductModel::getInstance()->tagRelation('.$this->exportArg($tag).');';

        $parseStr .= ' ?>';
        return $parseStr;
    }

    public function tagPrev($tag){
        $var  = isset($tag['var']) ? $tag['var'] : 'prev';

        $parseStr = '<?php ';

        $parseStr .= '$'.$var.'=\app\common\model\ProductModel::getInstance()->tagPrev('.$this->exportArg($tag).');';

        $parseStr .= ' ?>';
        return $parseStr;
    }
    public function tagNext($tag){
        $var  = isset($tag['var']) ? $tag['var'] : 'prev';

        $parseStr='<?php ';

        $parseStr .= '$'.$var.'=\app\common\model\ProductModel::getInstance()->tagNext('.$this->exportArg($tag).');';

        $parseStr .= ' ?>';
        return $parseStr;
    }

    public function tagCates($tag){
        $var  = isset($tag['var']) ? $tag['var'] : 'cates_list';
        $pid = isset($tag['pid']) ? $this->parseArg($tag['pid']) : -1;

        $parseStr='<?php ';

        $parseStr.='$'.$var.'=\think\Db::name("ProductCategory")';
        if($pid>-1){
            $parseStr .= "->where('pid',".$pid.")";
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

        $parseStr.='$'.$var.'=\app\common\facade\ProductCategoryFacade::findCategory('.$name.');';

        $parseStr .= ' ?>';
        return $parseStr;
    }
}