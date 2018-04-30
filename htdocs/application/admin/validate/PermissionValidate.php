<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/4/30
 * Time: 11:30
 */

namespace app\index\validate;


use app\common\validate\BaseUniqueValidate;

class PermissionValidate extends BaseUniqueValidate
{
    protected $rule=array(
        'name'=>'require',
        'key'=>'require|unique,permission,%id%'
    );

    protected $message=array(
        'name.require'=>'请填写菜单名称',
        'key.require'=>'请填写菜单键名',
        'key.unique'=>'键名已存在'
    );
}