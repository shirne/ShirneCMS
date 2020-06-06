<?php

namespace app\common\model;


use app\common\core\BaseModel;
use think\Db;

/**
 * Class ProductCommentModel
 * @package app\common\model
 */
class ProductCommentModel extends BaseModel
{
    protected $autoWriteTimestamp = true;

    protected $insert = ['status' ,'ip','device'];

    public static function init()
    {
        parent::init();

        self::afterInsert(function($model){
            $product_id = $model['product_id'];
            
            Db::name('product')->where('id',$product_id)->setInc('comment',1);
        });
        self::afterDelete(function($model){
            $product_id = $model['product_id'];
            
            Db::name('product')->where('id',$product_id)->setDec('comment',1);
        });
    }

    protected function setDeviceAttr()
    {
        return request()->isMobile()?'mobile':'pc';
    }

    protected function setIpAttr()
    {
        return request()->ip();
    }
    protected function setStatusAttr($value)
    {
        return empty($value)?0:intval($value);
    }
}