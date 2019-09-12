<?php

namespace app\common\model;


use app\common\core\BaseModel;
use think\Db;

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
    
    /**
     * 发放优惠券给用户
     * @param $user_ids
     */
    public function sendto($user_ids)
    {
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
            'coupon_id'=>$this['coupon_id'],
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
        $user_counts=[];
        if($this['count_limit']>0){
            $user_gets=Db::name('memberCoupon')->whereIn('member_id',$user_ids)->where('coupon_id',$this['id'])->field('member_id,sum(id) as coupon_counts')->group('member_id')->select();
            $user_counts = array_column($user_gets,'coupon_counts','member_id');
        }
        
        $sended=0;
        $stock=$this['stock'];
        foreach ($users as $user){
            if($this['count_limit']>0){
                if(isset($user_counts[$user['id']]) && $user_counts[$user['id']]>=$this['count_limit']){
                    $this->setError('会员['.$user['id'].']领取数量达到上限');
                    continue;
                }
            }
            if($stock==0){
                $this->setError('优惠券发完了');
                break;
            }
            $data['member_id']=$user['id'];
            Db::name('memberCoupon')->insert($data);
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
     * 检查可用优惠券 todo
     * @param $products
     * @param bool $strict
     */
    public static function checkCoupon($products,$strict=true){
    
    }
    
    /**
     * 检查用户可用优惠券 todo
     * @param $products
     * @param bool $strict
     */
    public static function checkMemberCoupon($products,$strict=true){
    
    }
    
    /**
     * 使用优惠券 todo
     * @param $member_coupon_id
     */
    public function useCoupon($member_coupon_id){
    
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