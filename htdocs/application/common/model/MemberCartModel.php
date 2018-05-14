<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/5/13
 * Time: 23:59
 */

namespace app\common\model;


use think\Db;
use think\Model;

class MemberCartModel extends Model
{
    public function addCart($product,$sku_id,$count,$member_id)
    {
        $exists=Db::name('MemberCart')->where([
            'member_id'=>$member_id,
            'sku_id'=>$sku_id,
        ])->find();
        if(!empty($exists)){
            return Db::name('MemberCart')->where([
                'member_id'=>$member_id,
                'sku_id'=>$sku_id,
            ])->setInc('count',$count);
        }else {
            return Db::name('MemberCart')->insert([
                'member_id' => $member_id,
                'product_id' => $product['id'],
                'sku_id' => $sku_id,
                'product_title' => $product['title'],
                'product_image' => $product['image'],
                'count' => $count
            ]);
        }
    }
    public function updateCart($sku_id,$count,$member_id)
    {
        return Db::name('MemberCart')->where([
            'member_id'=>$member_id,
            'sku_id'=>$sku_id,
        ])->update([
            'count'=>$count
        ]);
    }
    public function getCart($member_id, $sku_ids='')
    {
        $model=Db::name('MemberCart')->where('member_id',$member_id);
        if(!empty($sku_ids)){
            $model->where('sku_id','in',idArr($sku_ids));
        }
        return $model->select();
    }
    public function delCart($sku_ids,$member_id)
    {
        return Db::name('MemberCart')
            ->where('member_id',$member_id)
            ->where('sku_id',idArr($sku_ids))
        ->delete();
    }
    public function clearCart($member_id)
    {
        return Db::name('MemberCart')->where('member_id',$member_id)->delete();
    }
}