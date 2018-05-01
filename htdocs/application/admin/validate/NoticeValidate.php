<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/4/30
 * Time: 11:02
 */

namespace app\admin\validate;


use think\Validate;

class NoticeValidate extends Validate
{
    protected $rule=array(
        'title'=>'require'
    );
    protected $message=array(
        'title'=>'请填写公告标题'
    );

}