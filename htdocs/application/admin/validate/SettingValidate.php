<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/4/30
 * Time: 11:41
 */

namespace app\index\validate;


use app\common\validate\BaseUniqueValidate;

class SettingValidate extends BaseUniqueValidate
{
    protected $rule=array(
        'key'=>'require|unique,setting,%id%'
    );
    protected $message=array(
        'key.require'=>'请填写配置名',
        'key.unique'=>'配置名已经存在',
        'title.require'=>'请填写配置标题',
        'value.require'=>'请填写配置值'
    );
}