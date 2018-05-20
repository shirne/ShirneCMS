<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/5/2
 * Time: 20:07
 */

namespace app\common\taglib;


use think\template\TagLib;

class Article extends TagLib
{
    protected $tags =[
        'list'=>['attr'=>'var,category,type,limit,cover,recursive','close'=>0],
        'pages'=>['attr'=>'var,group,limit','close'=>0],
        'page'=>['attr'=>'var,name','close'=>0],
        'cates'=>['attr'=>'var,pid','close'=>0],
        'cate'=>['attr'=>'var,name','close'=>0],
        'listwrap'=>['attr'=>'name,step,id']
    ];

    public function tagList($tag){
        $var  = isset($tag['var']) ? $tag['var'] : 'article_list';
        $recursive =isset($tag['recursive']) ? $tag['recursive'] : 'false';
        $category=isset($tag['category']) ? $tag['category'] : '';
        if(preg_match('/^[a-zA-Z]\w*$/',$category)){
            $category="\\app\\common\\facade\\CategoryFacade::getCategoryId('".$category."')";
        }else{
            $category=intval($category);
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
        if(empty($tag['limit'])){
            $tag['limit']=10;
        }
        $parseStr .= '->limit('.intval($tag['limit']).')';
        $parseStr .= '->select();';

        $parseStr .= ' ?>';
        return $parseStr;
    }
    public function tagPages($tag){
        $var  = isset($tag['var']) ? $tag['var'] : 'page_list';
        $group=isset($tag['group']) ? $tag['group'] : '';

        $parseStr='<?php ';

        $parseStr.='$'.$var.'=\think\Db::name("page")';
        if(!empty($group)){
            $parseStr .= '->where(\'group\',\''.$group.'\')';
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
        $name=isset($tag['name']) ? $tag['name'] : '';

        $parseStr='<?php ';

        $parseStr.='$'.$var.'=\think\Db::name("page")';
        if(!empty($group)){
            $parseStr .= '->where(\'name\',\''.$name.'\')';
        }
        $parseStr .= '->find();';

        $parseStr .= ' ?>';
        return $parseStr;
    }

    public function tagCates($tag){
        $var  = isset($tag['var']) ? $tag['var'] : 'cates_list';
        $pid = isset($tag['pid']) ? intval($tag['pid']) : 0;

        $parseStr='<?php ';

        $parseStr.='$'.$var.'=\think\Db::name("Category")';
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