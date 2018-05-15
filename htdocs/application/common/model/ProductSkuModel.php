<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/5/15
 * Time: 22:51
 */

namespace app\common\model;


use think\Model;

class ProductSkuModel extends Model
{
    protected $pk='sku_id';
    protected $type = ['specs'=>'array'];
}