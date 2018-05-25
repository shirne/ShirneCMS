<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/5/2
 * Time: 20:31
 */

namespace app\common\taglib;


use think\template\TagLib;

class Extend extends TagLib
{
    protected $tags =[
        'links'=>['attr'=>'var,limit','close'=>0],
        'advs'=>['attr'=>'var,flag,limit','close'=>0],
        'notices'=>['attr'=>'var,limit','close'=>0]
    ];

    public function tagLinks($tag){
        $var  = isset($tag['var']) ? $tag['var'] : 'links';

        $parseStr='<?php ';

        $parseStr.='$'.$var.'=\think\Db::name("Links")';
        if(!empty($tag['limit'])){
            $parseStr .= '->limit('.intval($tag['limit']).')';
        }
        $parseStr .= '->select();';

        $parseStr .= ' ?>';
        return $parseStr;
    }

    public function tagAdvs($tag){
        $var  = isset($tag['var']) ? $tag['var'] : 'links';
        $limit=empty($tag['limit'])?'':', '.intval($tag['limit']);

        $parseStr='<?php ';

        $parseStr.='$'.$var.'=\app\common\model\AdvGroupModel::getAdList("'.$tag['flag'].'"'.$limit.');';

        $parseStr .= ' ?>';
        return $parseStr;
    }

    public function tagNotices($tag){
        $var  = isset($tag['var']) ? $tag['var'] : 'links';

        $parseStr='<?php ';

        $parseStr.='$'.$var.'=\think\Db::name("Notice")';
        $parseStr .= "->where('status',1)";
        if(!empty($tag['limit'])){
            $parseStr .= '->limit('.intval($tag['limit']).')';
        }
        $parseStr .= '->select();';

        $parseStr .= ' ?>';
        return $parseStr;
    }
}