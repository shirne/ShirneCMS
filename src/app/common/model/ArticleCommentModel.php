<?php

namespace app\common\model;


use app\common\core\BaseModel;
use think\facade\Db;

class ArticleCommentModel extends BaseModel
{
    protected $name = 'article_comment';
    protected $autoWriteTimestamp = true;

    protected $insert = ['status' ,'ip','device'];

    protected function setDeviceAttr()
    {
        return request()->isMobile()?'mobile':'pc';
    }

    protected function setIpAttr()
    {
        return request()->ip();
    }
    protected function setStatusAttr($value)
    {
        return empty($value)?0:intval($value);
    }

    public static function checkSubmitByMember($article_id, $member_id){
        return static::checkSubmit($article_id,'member_id',$member_id);
    }

    public static function checkSubmitByIP($article_id){
        $ip = request()->ip();
        return static::checkSubmit($article_id,'ip',$ip);
    }

    
    private static function checkSubmit($article_id, $field, $value){
        //是否有未审核评论
        $unaudit = Db::name('articleComment')->where($field, $value)->where('status',0)
            ->where('create_time','GT',time()-60*5)->count();
        if($unaudit>0){
            return false;
        }

        //一小时内同一篇文章评论数
        $count = Db::name('articleComment')->where('article_id',$article_id)->where($field, $value)
            ->where('create_time','GT',time()-60*60)->count();
        if($count>=2){
            return false;
        }
        //一小时内全部评论数
        $count = Db::name('articleComment')->where($field, $value)
            ->where('create_time','GT',time()-60*60)->count();
        if($count>=5){
            return false;
        }

        return true;
    }
}