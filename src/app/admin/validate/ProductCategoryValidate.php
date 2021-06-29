<?php

namespace app\admin\validate;


use app\common\core\BaseUniqueValidate;

/**
 * 商品分类数据验证
 * Class ProductCategoryValidate
 * @package app\admin\validate
 */
class ProductCategoryValidate extends BaseUniqueValidate
{
    protected $rule=array(
        'title'=>'require',
        'short'=>'max:20',
        'name'=>['require','unique'=>'productCategory,%id%', 'regex' => '/^[a-zA-Z]\w+$/']
    );
    protected $message=array(
        'title.require'=>'请填写分类标题',
        'name.require'=>'请填写分类别名',
        'name.unique'=>'分类别名已存在',
        'short.max'=>'简称长度不能超过20'
    );
}