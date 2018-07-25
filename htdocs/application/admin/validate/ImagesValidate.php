<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/7/25
 * Time: 18:04
 */

namespace app\admin\validate;


use think\Validate;

class ImagesValidate extends Validate
{
    protected $rule=array(
        'title|图片名称'=>'require'
    );
}