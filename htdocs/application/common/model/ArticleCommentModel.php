<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/5/5
 * Time: 9:33
 */

namespace app\common\model;


use think\Model;

class ArticleCommentModel extends Model
{
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
}