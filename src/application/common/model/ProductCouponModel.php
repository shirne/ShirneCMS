<?php

namespace app\common\model;


use app\common\core\BaseModel;
use app\common\facade\ProductCategoryFacade;
use think\Db;
use think\facade\Log;

class ProductCouponModel extends BaseModel
{
    protected $type = ['levels_limit'=>'array'];
    
    public static $coupon_types=[
        0=>'通用券',
        1=>'类目券',
        2=>'品牌券',
        3=>'商品券',
        4=>'规格券'
    ];

    protected function setStartTimeAttr($value=0)
    {
        if(is_int($value))return $value;
        if(is_numeric($value))return intval($value);
        return strtotime($value);
    }

    protected function setEndTimeAttr($value=0)
    {
        if(is_int($value))return $value;
        if(is_numeric($value))return intval($value);
        return strtotime($value);
    }

    protected function setExpiryTimeAttr($value=0)
    {
        if(is_int($value))return $value;
        if(is_numeric($value))return intval($value);
        return strtotime($value);
    }
    protected function setExpiryDayAttr($value=0)
    {
        return intval($value);
    }
    
    /**
     * 发放优惠券给用户
     * @param $user_ids
     */
    public function sendto($user_ids)
    {
        if($this->isEmpty())return false;
        if($this['status']!=1){
            $this->setError('优惠券已失效');
            return false;
        }
        if($this['stock']==0){
            $this->setError('优惠券已领完');
            return false;
        }
        if($this['start_time']>0 && $this['start_time']>time()){
            $this->setError('优惠券未到领取时间');
            return false;
        }
        if($this['end_time']>0 && $this['end_time']<time()){
            $this->setError('优惠券已过期');
            return false;
        }
        if(empty($user_ids))return true;
        
        if(!is_array($user_ids)){
            $user_ids = idArr($user_ids);
        }
        $model = MemberModel::whereIn('id',$user_ids)->where('status',1);
        if(!empty($this['levels_limit'])){
            $model->whereIn('level_id',$this['levels_limit']);
        }
        $users=$model->select();
        $data=[
            'coupon_id'=>$this['id'],
            'title'=>$this['title'],
            'bind_type'=>$this['bind_type'],
            'cate_id'=>$this['cate_id'],
            'brand_id'=>$this['brand_id'],
            'product_id'=>$this['product_id'],
            'sku_id'=>$this['sku_id'],
            'type'=>$this['type'],
            'limit'=>$this['limit'],
            'amount'=>$this['amount'],
            'discount'=>$this['discount'],
            'create_time'=>time(),
            'status'=>1,
            'use_time'=>0
        ];
        if($this['expiry_type']==1){
            $data['expiry_time']=$this['expiry_time'];
        }else{
            $data['expiry_time']=strtotime(date('Y-m-d 23:59:59'))+$this['expiry_day']*24*60*60;
        }
        $user_counts=[];
        if($this['count_limit']>0){
            $user_gets=Db::name('memberCoupon')->whereIn('member_id',$user_ids)->where('coupon_id',$this['id'])->field('member_id,count(id) as coupon_counts')->group('member_id')->select();
            $user_counts = array_column($user_gets,'coupon_counts','member_id');
        }
        
        $sended=0;
        $stock=$this['stock'];
        foreach ($users as $user){
            if($this['count_limit']>0){
                if(isset($user_counts[$user['id']]) && $user_counts[$user['id']]>=$this['count_limit']){
                    $this->setError('会员['.$user['nickname'].']领取数量达到上限');
                    continue;
                }
            }
            if($stock==0){
                $this->setError('优惠券发完了');
                break;
            }
            $data['member_id']=$user['id'];
            Db::name('memberCoupon')->insert($data);
            $sended++;
            if($stock>0)$stock--;
        }
        if($sended>0){
            $this->save([
                'stock'=>$stock,
                'receive'=>['INC',$sended]
            ]);
        }
        
        return $sended>0;
    }

    /**
     * 检查优惠券是否可用
     * @param mixed $products 
     * @param mixed $coupon 
     * @return bool|array 
     */
    public static function checkCoupon($products, $coupon){
        $total_price = 0;
        $items=[];
        if($coupon['bind_type'] == 0){
            foreach($products as $product){
                if($product['is_coupon'] == 1){
                    $items[$product['sku_id']]= round($product['product_price'] * $product['count'],2);
                }
            }
        }elseif($coupon['bind_type'] == 1){
            $subcates = ProductCategoryFacade::getSubCateIds($coupon['cate_id']);
            foreach($products as $product){
                if(in_array($product['cate_id'],$subcates) && $product['is_coupon'] == 1){
                    $items[$product['sku_id']]=round($product['product_price'] * $product['count'],2);
                }
            }
        }elseif($coupon['bind_type'] == 2){
            foreach($products as $product){
                if($product['brand_id'] == $coupon['brand_id'] && $product['is_coupon'] == 1){
                    $items[$product['sku_id']]=round($product['product_price'] * $product['count'],2);
                }
            }
            
        }elseif($coupon['bind_type'] == 3){
            foreach($products as $product){
                if((isset($product['product_id']) && $product['product_id'] == $coupon['product_id']) || 
                    (!isset($product['product_id']) && $product['id'] == $coupon['product_id'])){
                    $items[$product['sku_id']]=round($product['product_price'] * $product['count'],2);
                }
            }
        }elseif($coupon['bind_type'] == 4){
            foreach($products as $product){
                if($product['sku_id'] == $coupon['sku_id'] && $product['product_id'] == $coupon['product_id']){
                    $items[$product['sku_id']]=round($product['product_price'] * $product['count'],2);
                }
            }
        }
        if(empty($items)){
            return false;
        }
        $total_price = array_sum($items);
        $result=[
            'total'=>0
        ];
        if($coupon['type'] == 1){
            if($total_price <= 0){
                return false;
            }
            $result['total'] = $total_price * $coupon['discount']/100;
        }else{
            if($total_price < $coupon['limit']){
                return false;
            }
            $result['total'] = $coupon['amount'];
        }
        $discounts=[];
        $last_sku_id = 0;
        foreach($items as $sku_id=>$subtotal){
            $discounts[$sku_id] = round($result['total'] * $subtotal / $total_price,2);
            $last_sku_id = $sku_id;
        }
        // 修正总数
        $release = array_sum($discounts);
        if($release != $result['total']){
            $discounts[$last_sku_id] += $result['total'] - $release;
        }
        $result['discounts']=$discounts;

        return $result;
    }
    
    /**
     * 检查可用优惠券 todo
     * @param $products
     * @param bool $strict
     * @return array
     */
    public static function matchCoupons($products,$strict=true){
        $cates=[];
        $brands=[];
        $prods=[];
        $skus=[];
        foreach($products as $product){
            $prods[] = $product['id'];
            $cates += ProductCategoryFacade::getParents($product['cate_id']);
            $brands[] = $product['brand_id'];
            if(isset($product['sku_id'])){
                $skus[]=$product['sku_id'];
            }
        }
        $prods = array_unique($prods);
        $cates = array_unique($cates);
        $brands = array_unique($brands);

        if(empty($skus)){
            $productskus = Db::name('productSku')->whereIn('product_id',$prods)->select();
            $skus = array_column($productskus,'sku_id');
        }

        // 通用优惠券
        $coupons = static::where('bind_type',0)->where('status',1)->where('stock','<>',0)->select();

        // 类目
        $coupons_cate = static::where('bind_type',1)->whereIn('cate_id',$cates)->where('status',1)->where('stock','<>',0)->select();

        // 品牌
        $coupons_brand = static::where('bind_type',2)->whereIn('brand_id',$brands)->where('status',1)->where('stock','<>',0)->select();

        // 商品
        $coupons_prod = static::where('bind_type',3)->whereIn('product_id',$prods)->where('status',1)->where('stock','<>',0)->select();

        // SKU
        $coupons_sku = static::where('bind_type',4)->whereIn('product_id',$prods)->whereIn('sku_id',$skus)->where('status',1)->where('stock','<>',0)->select();

        return $coupons + $coupons_cate + $coupons_brand + $coupons_prod + $coupons_sku;
    }
    
    /**
     * 检查用户可用优惠券 todo
     * @param $products
     * @param bool $strict
     */
    public static function matchMemberCoupons($products,$member_id,$strict=true){
        Log::info(var_export($products,true));
        $cates=[];
        $brands=[];
        $prods=[];
        $skus=[];
        foreach($products as $product){
            $prods[] = isset($product['product_id'])?$product['product_id']:$product['id'];
            $cates += ProductCategoryFacade::getParents($product['cate_id']);
            $brands[] = $product['brand_id'];
            if(isset($product['sku_id'])){
                $skus[]=$product['sku_id'];
            }
        }
        $prods = array_unique($prods);
        $cates = array_unique($cates);
        $brands = array_unique($brands);
        if(empty($skus)){
            $productskus = Db::name('productSku')->whereIn('product_id',$prods)->select();
            $skus = array_column($productskus,'sku_id');
        }

        // 通用优惠券
        $coupons = Db::name('memberCoupon')->where('bind_type',0)->where('status',1)->where('member_id',$member_id)->select();

        // 类目
        $coupons_cate = Db::name('memberCoupon')->where('bind_type',1)->whereIn('cate_id',$cates)->where('status',1)->where('member_id',$member_id)->select();

        // 品牌
        $coupons_brand = Db::name('memberCoupon')->where('bind_type',2)->whereIn('brand_id',$brands)->where('status',1)->where('member_id',$member_id)->select();

        // 商品
        $coupons_prod = Db::name('memberCoupon')->where('bind_type',3)->whereIn('product_id',$prods)->where('status',1)->where('member_id',$member_id)->select();

        // SKU
        $coupons_sku = Db::name('memberCoupon')->where('bind_type',4)->whereIn('product_id',$prods)->whereIn('sku_id',$skus)->where('status',1)->where('member_id',$member_id)->select();

        return $coupons + $coupons_cate + $coupons_brand + $coupons_prod + $coupons_sku;
    }
    
    /**
     * 使用优惠券 todo
     * @param $member_coupon_id
     */
    public function useCoupon($member_coupon_id){
        $coupon = Db::name('memberCoupon')->where('id',$member_coupon_id)->find();
        if(empty($coupon)){
            $this->setError('优惠券不存在');
            return false;
        }
        if($coupon['status'] != 1){
            $this->setError('优惠券'.($coupon['status']==2 ? '已使用':'已失效'));
            return false;
        }
        if($coupon['expiry_time'] < time()){
            $this->setError('优惠券已过期');
            Db::name('memberCoupon')->where('id',$member_coupon_id)->update(['status'=>0]);
            return false;
        }
        Db::name('memberCoupon')->where('id',$member_coupon_id)->update(['status'=>2,'use_time'=>time()]);
        return true;
    }


    /**
     * 锁定优惠券
     * @param mixed $member_coupon_id 
     * @return void 
     */
    public function lockCoupon($member_coupon_id, $order_id){
        
    }

    /**
     * 释放优惠券
     * @param mixed $member_coupon_id 
     * @return void 
     */
    public function releaseCoupon($member_coupon_id){
    
    }
    
    /**
     * 单用户领取优惠券
     */
    public function getCoupon($user){
        if($this->isEmpty())return false;
        if($this['status']!=1){
            $this->setError('优惠券已禁用');
            return false;
        }
        if($this['start_time']>0 && $this['start_time']>time()){
            $this->setError('优惠券未到领取时间');
            return false;
        }
        if($this['end_time']>0 && $this['end_time']<time()){
            $this->setError('优惠券已过期领取时间');
            return false;
        }
        if(empty($user))return false;
        if(!is_array($user)){
            $user = MemberModel::get(intval($user));
        }
        if(empty($user))return false;
        if(!empty($this['levels_limit'])){
            if(!in_array($user['level_id'],$this['levels_limit'])){
                $this->setError('您当前会员组不能领取该优惠券');
                return false;
            }
        }
        if($this['count_limit']>0){
            $counts=Db::name('memberCoupon')->whereIn('member_id',$user['id'])->where('coupon_id',$this['id'])->count();
            if($counts>$this['count_limit']){
                $this->setError('优惠券每人限领'.$this['count_limit'].'张');
                return false;
            }
        }
        if($this['stock']==0){
            $this->setError('优惠券已领完');
            return false;
        }
        $data=[
            'coupon_id'=>$this['coupon_id'],
            'member_id'=>$user['id'],
            'title'=>$this['title'],
            'bind_type'=>$this['bind_type'],
            'cate_id'=>$this['cate_id'],
            'brand_id'=>$this['brand_id'],
            'product_id'=>$this['product_id'],
            'sku_id'=>$this['sku_id'],
            'type'=>$this['type'],
            'limit'=>$this['limit'],
            'amount'=>$this['amount'],
            'discount'=>$this['discount'],
            'create_time'=>time(),
            'status'=>1,
            'use_time'=>0
        ];
        if($this['expiry_type']==1){
            $data['expiry_time']=$this['expiry_day'];
        }else{
            $data['expiry_time']=strtotime('Y-m-d 23:59:59')+$this['expiry_day']*24*60*60;
        }
        Db::name('memberCoupon')->insert($data);
        $this->save([
            'stock'=>$this['stock']>0?($this['stock']-1):-1,
            'receive'=>['INC',1]
        ]);
        return true;
    }
}