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
        'list'=>['attr'=>'var,category,type,limit,cover','close'=>0],
        'pages'=>['attr'=>'var,group,limit','close'=>0],
        'cates'=>['attr'=>'var,pid','close'=>0]
    ];

    public function tagList($tag){
        $var  = isset($tag['var']) ? $tag['var'] : 'article_list';
        $recursive =isset($tag['recursive']) ? $tag['recursive'] : 'false';

        $parseStr='<?php ';

        $parseStr.='$'.$var.'=\think\Db::view("Article","*")';
        $parseStr .= '->view("Category",["title"=>"category_title","name"=>"category_name","short"=>"category_short","icon"=>"category_icon","image"=>"category_image"],"Article.cate_id=Category.id","LEFT")';
        if(!empty($tag['category'])){
            if($recursive=='true'){
                $parseStr .= '->where("Article.cate_id", "IN", getSubCateids(' . intval($tag['category']) . '))';
            }else {
                $parseStr .= '->where("Article.cate_id",' . intval($tag['category']) . ')';
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
            $parseStr .= '->where(\'group\',\''.$tag['group'].'\')';
        }
        if(!empty($tag['limit'])){
            $parseStr .= '->limit('.intval($tag['limit']).')';
        }
        $parseStr .= '->select();';

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
}