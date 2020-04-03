<?php


namespace app\common\model;

use app\common\core\BaseModel;
use think\facade\Db;

/**
 * Class MemberCartModel
 * @package app\common\model
 */
class MemberCartModel extends BaseModel
{
    protected $name = 'member_cart';
    
    private function getSort($member_id){
        $sort=Db::name('MemberCart')->where('member_id',$member_id)
            ->max('sort');

        return $sort?($sort+1):1;
    }
    public function mapCart($product,$sku){
        return [
            'product_id' => $product['id'],
            'sku_id' => $sku['sku_id'],
            'product_title' => $product['title'],
            'product_image' => $sku['image']?$sku['image']:$product['image'],
            'product_price' => $sku['price'],
            'product_weight' => $sku['weight']
        ];
    }
    public function mapProduct($product,$sku){
        $data=[];
        $productMaps=['id'=>'product_id','title'=>'product_title','image'=>'product_image','spec_data','levels','is_discount','postage_id','is_commission','type'];
        $skuMaps=['sku_id','storage','sale','goods_no'=>'sku_goods_no','weight'=>'product_weight','specs','price'=>'product_price','ext_price'=>'ext_price','market_price','cost_price','image'=>'sku_image'];
        foreach ($productMaps as $k=>$v){
            if(is_string($k)){
                $data[$v]=$product[$k];
            }else{
                $data[$v]=$product[$v];
            }
        }
        foreach ($skuMaps as $k=>$v){
            if(is_string($k)){
                $data[$v]=$sku[$k];
            }else{
                $data[$v]=$sku[$v];
            }
        }
        return $data;
    }
    public function getCount($member_id)
    {
        return Db::name('MemberCart')
            ->where('member_id',$member_id)
            ->count('id');
    }
    public function addCart($product,$sku,$count,$member_id)
    {
        $sort=$this->getSort($member_id);
        $exists=Db::name('MemberCart')->where([
            'member_id'=>$member_id,
            'sku_id'=>$sku['sku_id'],
        ])->find();
        if(!empty($exists)){
            return Db::name('MemberCart')->where([
                'member_id'=>$member_id,
                'sku_id'=>$sku['sku_id'],
            ])->inc('count',$count)->update(['sort'=>$sort]);
        }else {
            $data=$this->mapCart($product,$sku);
            $data['member_id']=$member_id;
            $data['count']=$count;
            $data['sort']=$sort;
            return Db::name('MemberCart')->insert($data);
        }
    }
    public function updateCartData($product,$sku,$member_id,$id)
    {
        $data=$this->mapCart($product,$sku);
        return Db::name('MemberCart')->where([
            'member_id'=>$member_id,
            'id'=>$id,
        ])->update($data);
    }
    public function updateCart($sku_id,$count,$member_id)
    {
        $sort=$this->getSort($member_id);
        return Db::name('MemberCart')->where([
            'member_id'=>$member_id,
            'sku_id'=>$sku_id,
        ])->update([
            'count'=>$count,
            'sort'=>$sort
        ]);
    }
    public function getCart($member_id, $sku_ids='')
    {
        $model = Db::name('MemberCart')->where('member_id',$member_id);
        if(!empty($sku_ids)){
            $model->whereIn('sku_id',idArr($sku_ids));
        }
        $carts = $model->order('sort DESC')->select();
        $lists = [];
        if(!empty($carts)) {
            $datas = ProductModel::getForOrder(array_column($carts, 'count', 'sku_id'));
            $datas = array_column($datas, NULL, 'sku_id');
            foreach ($carts as $cart) {
                
                if(isset($datas[$cart['sku_id']])){
                    $item = $datas[$cart['sku_id']];
                    $item['id']=$cart['id'];
                }else{
                    //商品已删除或下架
                    $item=$cart;
                }
                $item['cart_product_price']=$cart['product_price'];
                $item['cart_product_image']=$cart['product_image'];
                $item['cart_product_title']=$cart['product_title'];
                $lists[]=$item;
            }
        }
        return $lists;
    }
    public function delCart($sku_ids,$member_id)
    {
        return Db::name('MemberCart')
            ->where('member_id',$member_id)
            ->whereIn('sku_id',idArr($sku_ids))
        ->delete();
    }
    public function clearCart($member_id)
    {
        return Db::name('MemberCart')->where('member_id',$member_id)->delete();
    }
}