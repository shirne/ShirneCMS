<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/4/30
 * Time: 11:39
 */

namespace app\admin\validate;


use app\common\validate\BaseUniqueValidate;

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