<?php

namespace app\common\taglib;

use app\common\core\BaseTabLib;

/**
 * Class Extendtag
 * @package app\common\taglib
 */
class Extendtag extends BaseTabLib
{
    protected $tags =[
        'keywords'=>['attr'=>'var,group,limit','close'=>0],
        'links'=>['attr'=>'var,group,limit','close'=>0],
        'advs'=>['attr'=>'var,flag,limit','close'=>0],
        'notices'=>['attr'=>'var,limit','close'=>0],
        'notice'=>['attr'=>'var,name','close'=>0],
        'feedback'=>['attr'=>'var,limit,page','close'=>0]
    ];

    public function tagKeywords($tag){
        $var  = isset($tag['var']) ? $tag['var'] : 'keywords';
        $group = isset($tag['group']) ? $this->parseArg($tag['group']) : '';

        $parseStr='<?php ';
        if(empty($group)){
            $group = "''";
        }
        $parseStr.='$'.$var.'=\app\common\model\KeywordsModel::getKeywords('.$group;
        
        if(!empty($tag['limit'])){
            $parseStr .= ','.intval($tag['limit']);
        }
        $parseStr .= ');';

        $parseStr .= ' ?>';
        return $parseStr;
    }

    public function tagLinks($tag){
        $var  = isset($tag['var']) ? $tag['var'] : 'links';
        $group = isset($tag['group']) ? $this->parseArg($tag['group']) : '';

        $parseStr='<?php ';

        $parseStr.='$'.$var.'=\think\Db::name("Links")';
        if(!empty($group)){
            $parseStr .= '->where(\'group\','.$group.')';
        }
        $parseStr .= '->order("sort ASC,id ASC")';
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

        $parseStr.='$'.$var.'=\app\common\model\AdvGroupModel::getAdList('.$this->parseArg($tag['flag']) . $limit.');';

        $parseStr .= ' ?>';
        return $parseStr;
    }

    public function tagNotices($tag){
        $var  = isset($tag['var']) ? $tag['var'] : 'links';

        $parseStr='<?php ';

        $parseStr.='$'.$var.'=\think\Db::name("Notice")';
        $parseStr .= "->where('status',1)";
        $parseStr .= '->order("create_time DESC")';
        if(!empty($tag['limit'])){
            $parseStr .= '->limit('.intval($tag['limit']).')';
        }
        $parseStr .= '->select();';

        $parseStr .= ' ?>';
        return $parseStr;
    }

    public function tagNotice($tag){
        $var  = isset($tag['var']) ? $tag['var'] : 'notice_model';
        $name=isset($tag['name']) ? $this->parseArg($tag['name']) : '';

        $parseStr='<?php ';

        $parseStr.='$'.$var.'=\think\Db::name("Notice")';
        $parseStr .= '->where(\'status\',1)';
        if(!empty($name)){
            $parseStr .= '->where(\'page\','.$name.')';
        }
        $parseStr .= '->find();';

        $parseStr .= ' ?>';
        return $parseStr;
    }

    public function tagFeedback($tag){
        $var  = isset($tag['var']) ? $tag['var'] : 'feedbacks';

        $parseStr='<?php ';

        $parseStr.='$'.$var.'=\think\Db::view("Feedback","*")';
        $parseStr .= '->view("member",["username","nickname","avatar"],"Feedback.member_id=member.id","LEFT")';
        $parseStr .= '->view("manager",["realname"=>"manager_name"],"Feedback.manager_id=manager.id","LEFT")';
        $parseStr .= '->where("Feedback.status",1)';
        $parseStr .= '->order("Feedback.create_time DESC")';

        if($tag['page']=='1'){
            $parseStr .= '->paginate(' . intval($tag['limit']) . ');';
        }else {
            if (!empty($tag['limit'])) {
                $parseStr .= '->limit(' . intval($tag['limit']) . ')';
            }
            $parseStr .= '->select();';
        }
        $parseStr .= ' ?>';
        return $parseStr;
    }
}