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
        'goods_no'=>'require|alphaDash|unique:productSku,%id%,sku_id',
        'price'=>'require,number',
        'market_price'=>'require,number',
        'cost_price'=>'require,number',
        'weight'=>'require,integer',
        'storage'=>'require,integer'
    );
    protected $message=array(
        'goods_no.require'=>'请填写规格货号',
        'goods_no.unique'=>'规格货号已存在',
        'goods_no.alphaDash'=>'商品货号只能填写字母，数字及_',
        'price.require'=>'请填写规格价格',
        'price.number'=>'价格必须填写数字',
        'market_price.require'=>'请填写规格市场价',
        'market_price.number'=>'价格必须填写数字',
        'cost_price.require'=>'请填写规格成本价',
        'cost_price.number'=>'价格必须填写数字',
        'weight.require'=>'请填写规格重量',
        'weight.integer'=>'重量单位为g 只需要填写整数',
        'storage.require'=>'请填写规格库存',
        'storage.integer'=>'库存请填写整数'
    );

}