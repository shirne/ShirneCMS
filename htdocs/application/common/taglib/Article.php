<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/5/2
 * Time: 20:07
 */

namespace app\common\taglib;


class Article extends BaseTabLib
{
    protected $tags =[
        'list'=>['attr'=>'var,category,type,order,limit,cover,recursive','close'=>0],
        'prev'=>['attr'=>'var,category,id','close'=>0],
        'next'=>['attr'=>'var,category,id','close'=>0],
        'pages'=>['attr'=>'var,group,limit','close'=>0],
        'page'=>['attr'=>'var,name','close'=>0],
        'cates'=>['attr'=>'var,pid','close'=>0],
        'cate'=>['attr'=>'var,name','close'=>0],
        'listwrap'=>['attr'=>'name,step,id']
    ];

    public function tagList($tag){
        $var  = isset($tag['var']) ? $tag['var'] : 'article_list';
        $recursive =isset($tag['recursive']) ? $tag['recursive'] : 'false';
        $category=isset($tag['category']) ? $this->parseArg($tag['category']) : 0;
        $order=isset($tag['order']) ? $tag['order'] : 'id DESC';
        if(strpos($order,' ')<=0){
            $order.=' ASC';
        }
        if(is_string($category) && strpos($category,"'")===0){
            $category="\\app\\common\\facade\\CategoryFacade::getCategoryId(".$category.")";
        }

        $parseStr='<?php ';

        $parseStr.='$'.$var.'=\think\Db::view("Article","*")';
        $parseStr .= '->view("Category",["title"=>"category_title","name"=>"category_name","short"=>"category_short","icon"=>"category_icon","image"=>"category_image"],"Article.cate_id=Category.id","LEFT")';
        if(!empty($category)){
            if($recursive=='true'){
                $parseStr .= '->where("Article.cate_id", "IN", \app\common\facade\CategoryFacade::getSubCateIds(' . $category . '))';
            }else {
                $parseStr .= '->where("Article.cate_id",' . $category . ')';
            }
        }
        if(!empty($tag['type'])){
            $parseStr .= '->where("Article.type",'.intval($tag['type']).')';
        }
        if(!empty($tag['cover'])){
            $parseStr .= '->where("Article.cover","<>","")';
        }
        $parseStr .= '->order("Article.'.$order.'")';
        if(empty($tag['limit'])){
            $tag['limit']=10;
        }
        $parseStr .= '->limit('.intval($tag['limit']).')';
        $parseStr .= '->select();';

        $parseStr .= ' ?>';
        return $parseStr;
    }
    public function tagPrev($tag){
        $var  = isset($tag['var']) ? $tag['var'] : 'prev';
        $category=isset($tag['category']) ? $this->parseArg($tag['category']) : '';
        $id=isset($tag['id']) ? intval($tag['id']) : 0;
        if(is_string($category) && strpos($category,"'")===0){
            $category="\\app\\common\\facade\\CategoryFacade::getCategoryId(".$category.")";
        }

        $parseStr='<?php ';

        $parseStr.='$'.$var.'=\think\Db::view("Article","*")';
        $parseStr .= '->view("Category",["title"=>"category_title","name"=>"category_name","short"=>"category_short","icon"=>"category_icon","image"=>"category_image"],"Article.cate_id=Category.id","LEFT")';
        $parseStr .= '->where("Article.id", "LT", ' . $id . ')';
        if(!empty($category)){
            $parseStr .= '->where("Article.cate_id", "IN", \app\common\facade\CategoryFacade::getSubCateIds(' . $category . '))';
        }
        $parseStr .= '->order("Article.id DESC")';
        $parseStr .= '->find();';

        $parseStr .= ' ?>';
        return $parseStr;
    }
    public function tagNext($tag){
        $var  = isset($tag['var']) ? $tag['var'] : 'prev';
        $category=isset($tag['category']) ? $this->parseArg($tag['category']) : '';
        $id=isset($tag['id']) ? intval($tag['id']) : 0;
        if(is_string($category) && strpos($category,"'")===0){
            $category="\\app\\common\\facade\\CategoryFacade::getCategoryId(".$category.")";
        }

        $parseStr='<?php ';

        $parseStr.='$'.$var.'=\think\Db::view("Article","*")';
        $parseStr .= '->view("Category",["title"=>"category_title","name"=>"category_name","short"=>"category_short","icon"=>"category_icon","image"=>"category_image"],"Article.cate_id=Category.id","LEFT")';
        $parseStr .= '->where("Article.id", "GT", ' . $id . ')';
        if(!empty($category)){
            $parseStr .= '->where("Article.cate_id", "IN", \app\common\facade\CategoryFacade::getSubCateIds(' . $category . '))';
        }
        $parseStr .= '->order("Article.id ASC")';
        $parseStr .= '->find();';

        $parseStr .= ' ?>';
        return $parseStr;
    }
    public function tagPages($tag){
        $var  = isset($tag['var']) ? $tag['var'] : 'page_list';
        $group=isset($tag['group']) ? $this->parseArg($tag['group']) : '';

        $parseStr='<?php ';

        $parseStr.='$'.$var.'=\think\Db::name("page")';
        if(!empty($group)){
            $parseStr .= '->where(\'group\','.$group.')';
        }
        if(!empty($tag['limit'])){
            $parseStr .= '->limit('.intval($tag['limit']).')';
        }
        $parseStr .= '->select();';

        $parseStr .= ' ?>';
        return $parseStr;
    }
    public function tagPage($tag){
        $var  = isset($tag['var']) ? $tag['var'] : 'page_model';
        $name=isset($tag['name']) ? $this->parseArg($tag['name']) : '';

        $parseStr='<?php ';

        $parseStr.='$'.$var.'=\think\Db::name("page")';
        if(!empty($group)){
            $parseStr .= '->where(\'name\','.$name.')';
        }
        $parseStr .= '->find();';

        $parseStr .= ' ?>';
        return $parseStr;
    }

    public function tagCates($tag){
        $var  = isset($tag['var']) ? $tag['var'] : 'cates_list';
        $pid = isset($tag['pid']) ? $this->parseArg($tag['pid']) : 0;

        $parseStr='<?php ';

        $parseStr.='$'.$var.'=\think\Db::name("Category")';
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

        $parseStr.='$'.$var.'=\app\common\facade\CategoryFacade::findCategory('.$name.');';

        $parseStr .= ' ?>';
        return $parseStr;
    }
    public function tagListwrap($tag,$content){
        $name   = $tag['name'];
        $id     = isset($tag['id'])?$tag['id']:'wrapedlist';
        $step   = isset($tag['step'])?intval($tag['step']):1;
        $flag     = substr($name, 0, 1);

        $parseStr='<?php ';
        if (':' == $flag) {
            $name = $this->autoBuildVar($name);
            $parseStr .= '$_result=' . $name . ';';
            $name = '$_result';
        } else {
            $name = $this->autoBuildVar($name);
        }
        $parseStr .= '$wrapcount=count('.$name.');';
        $parseStr .= 'for($wrapi=0; $wrapi < $wrapcount; $wrapi+='.$step.'):';
        $parseStr .= ' $'.$id.' = array_slice('.$name.', $wrapi, '.$step.'); ?>';
        $parseStr .= $content;
        $parseStr .= '<?php endfor; ?>';
        return $parseStr;
    }
}