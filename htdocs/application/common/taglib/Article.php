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
        'pages'=>['attr'=>'var,limit','close'=>0]
    ];

    public function tagList($tag){
        $var  = isset($tag['var']) ? $tag['var'] : 'article_list';

        $parseStr='<?php ';

        $parseStr.='$'.$var.'=\think\Db::name("post")';
        if(!empty($tag['category'])){
            $parseStr .= '->where("cate_id",'.intval($tag['category']).');';
        }
        if(!empty($tag['type'])){
            $parseStr .= '->where("type",'.intval($tag['type']).');';
        }
        if(!empty($tag['cover'])){
            $parseStr .= '->where("cover","<>","");';
        }
        if(empty($tag['limit'])){
            $tag['limit']=10;
        }
        $parseStr .= '->limit('.intval($tag['limit']).');';
        $parseStr .= '->select();';

        $parseStr .= ' ?>';
        return $parseStr;
    }
    public function tagPages($tag){
        $var  = isset($tag['var']) ? $tag['var'] : 'page_list';

        $parseStr='<?php ';

        $parseStr.='$'.$var.'=\think\Db::name("page")';
        if(!empty($tag['limit'])){
            $parseStr .= '->limit('.intval($tag['limit']).');';
        }
        $parseStr .= '->select();';

        $parseStr .= ' ?>';
        return $parseStr;
    }
}