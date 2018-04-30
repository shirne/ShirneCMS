<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/4/30
 * Time: 10:23
 */

namespace app\index\validate;


use app\common\validate\BaseUniqueValidate;

class CategoryValidate extends BaseUniqueValidate
{

    protected $rule=array(
        'title'=>'require',
        'short|简称'=>'max:20',
        'name'=>'require|unique,category,%id%'
    );
    protected $message=array(
        'title'=>'请填写分类标题',
        'name.require'=>'请填写分类别名',
        'name.unique'=>'分类别名已存在'
    );
}