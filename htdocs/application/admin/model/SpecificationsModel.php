<?php

namespace app\admin\model;


use app\common\core\BaseModel;
use think\Db;

/**
 * Class SpecificationsModel
 * @package app\admin\model
 */
class SpecificationsModel extends BaseModel
{
    protected $type = ['data'=>'array'];

    public static function getList(){
        $data=Db::name('Specifications')->field('id,title')->select();
        $lists=[];
        foreach ($data as $row){
            $lists[$row['id']]=$row['title'];
        }
        return $lists;
    }
}