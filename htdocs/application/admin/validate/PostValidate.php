<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/4/30
 * Time: 11:39
 */

namespace app\index\validate;


use think\Validate;

class PostValidate extends Validate
{
    protected $rule=array(
        'title'=>'require'
    );
    protected $message=array(
        'title.require'=>'请填写文章标题'
    );

}