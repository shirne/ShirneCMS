<?php

namespace app\admin\validate;

use think\Validate;


/**
 * 优惠券数据验证
 * Class ProductCouponValidate
 * @package app\admin\validate
 */
class ProductCouponValidate extends Validate
{
    protected $rule = array(
        'title|优惠券名称' => 'require',
        'cate_id|优惠类目' => 'requireCallback:cate_require|number',
        'brand_id|优惠品牌' => 'requireCallback:brand_require|number',
        'product_id|优惠商品' => 'requireCallback:product_require|number',
        'sku_id|优惠规格' => 'requireCallback:sku_require|number',
        'discount|优惠折扣' => 'requireCallback:discount_require|number',
        'limit|购买满额' => 'requireCallback:limit_require|number',
        'amount|优惠金额' => 'requireCallback:limit_require|number'
    );

    public function cate_require($value, $data)
    {
        if ($data['bind_type'] == 1) {
            return true;
        }
        return false;
    }

    public function brand_require($value, $data)
    {
        if ($data['bind_type'] == 2) {
            return true;
        }
        return false;
    }

    public function product_require($value, $data)
    {
        if ($data['bind_type'] >= 3) {
            return true;
        }
        return false;
    }

    public function sku_require($value, $data)
    {
        if ($data['bind_type'] == 4) {
            return true;
        }
        return false;
    }

    public function discount_require($value, $data)
    {
        if ($data['type'] == 1) {
            return true;
        }
        return false;
    }

    public function limit_require($value, $data)
    {
        if ($data['type'] == 0) {
            return true;
        }
        return false;
    }
}
