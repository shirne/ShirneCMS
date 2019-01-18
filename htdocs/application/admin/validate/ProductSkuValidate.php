<?php

namespace app\admin\validate;


use app\common\validate\BaseUniqueValidate;

/**
 * 商品规格数据验证
 * Class ProductSkuValidate
 * @package app\admin\validate
 */
class ProductSkuValidate extends BaseUniqueValidate
{
    protected $rule=array(
        'goods_no'=>'require|unique:productSku,%id%,sku_id',
        'price'=>'require',
        'market_price'=>'require',
        'cost_price'=>'require',
        'weight'=>'require',
        'storage'=>'require'
    );
    protected $message=array(
        'goods_no.require'=>'请填写规格货号',
        'goods_no.unique'=>'规格货号已存在',
        'price.require'=>'请填写规格价格',
        'market_price.require'=>'请填写规格市场价',
        'cost_price.require'=>'请填写规格成本价',
        'weight.require'=>'请填写规格重量',
        'storage.require'=>'请填写规格库存'
    );
}