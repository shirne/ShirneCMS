<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/5/15
 * Time: 22:53
 */

namespace app\admin\model;


use think\Db;
use think\Model;

class SpecificationsModel extends Model
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