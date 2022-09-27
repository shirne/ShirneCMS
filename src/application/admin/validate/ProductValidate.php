<?php

namespace app\admin\validate;


use app\common\core\BaseUniqueValidate;

/**
 * 商品数据验证
 * Class ProductValidate
 * @package app\admin\validate
 */
class ProductValidate extends BaseUniqueValidate
{
    protected $rule=array(
        'title'=>'require',
        'vice_title'=>'max:200',
        'goods_no'=>'require|alphaDash|unique:product,%id%',
    );
    protected $message=array(
        'title.require'=>'请填写商品名称',
        'vice_title.max'=>'商品特性限制在200个字符以内',
        'goods_no.require'=>'请填写商品货号',
        'goods_no.unique'=>'商品货号已存在',
        'goods_no.alphaDash'=>'商品货号只能填写字母，数字及_'
    );
}