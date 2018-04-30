<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/4/30
 * Time: 12:00
 */

namespace app\index\validate;


use app\common\validate\BaseUniqueValidate;

class AdvGroupValidate extends BaseUniqueValidate
{
    protected $rule=array(
        'title|广告组名称'=>'require',
        'flag|调用标识'=>'require|unique,AdvGroup,%id%'
    );
}