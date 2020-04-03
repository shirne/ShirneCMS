<?php


namespace app\admin\validate;


use app\common\core\BaseUniqueValidate;

class BoothValidate extends BaseUniqueValidate
{
    protected $rule=array(
        'title|展位名称'=>'require',
        'flag|调用标识'=>'require|unique:Booth,%id%',
        'type|展位类型'=>'require'
    );
}