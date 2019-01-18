<?php

namespace app\admin\validate;


use app\common\validate\BaseUniqueValidate;

/**
 * 商品数据验证
 * Class ProductValidate
 * @package app\admin\validate
 */
class ProductValidate extends BaseUniqueValidate
{
    protected $rule=array(
        'title'=>'require',
        'goods_no'=>'require|unique:product,%id%',
    );
    protected $message=array(
        'title.require'=>'请填写商品名称',
        'goods_no.require'=>'请填写商品货号',
        'goods_no.unique'=>'商品货号已存在'
    );

}