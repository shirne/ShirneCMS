<?php

namespace app\admin\validate;


use app\common\validate\BaseUniqueValidate;

class GoodsValidate extends BaseUniqueValidate
{
    protected $rule=array(
        'title'=>'require',
        'goods_no'=>'require|unique:goods,%id%',
        'price'=>'require',
    );
    protected $message=array(
        'title.require'=>'请填写商品名称',
        'goods_no.require'=>'请填写商品货号',
        'goods_no.unique'=>'商品货号已存在',
        'price.require'=>'请填写商品价格'
    );

}