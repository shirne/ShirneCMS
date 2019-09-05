<?php

namespace app\common\model;


use app\common\core\BaseModel;

class ProductCouponModel extends BaseModel
{
    protected $type = ['levels_limit'=>'array'];
    
    /**
     * 发放优惠券给用户 todo
     * @param $user_ids
     */
    public function sendto($user_ids)
    {
        if(!is_array($user_ids)){
            $user_ids = explode(',',$user_ids);
        }
    }
}