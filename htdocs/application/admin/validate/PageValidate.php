<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/4/30
 * Time: 11:09
 */

namespace app\admin\validate;


use app\common\validate\BaseUniqueValidate;

class PageValidate extends BaseUniqueValidate
{

    protected $rule=array(
        'title'=>'require',
        'name'=>'require|unique:page,%id%'
    );
    protected $message = array(
        'title.require'=>'请填写单页标题！',
        'name.require'=>'请填写单页别名！',
        'name.unique'=>'单页别名已经存在！'
    );
}