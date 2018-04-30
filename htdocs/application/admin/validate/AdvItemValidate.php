<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/4/30
 * Time: 12:04
 */

namespace app\index\validate;


use think\Validate;

class AdvItemValidate extends Validate
{
    protected $rule=array(
        'title|广告名称'=>'require'
    );
}