<?php

namespace app\common\model;


use think\Db;
use think\Model;

/**
 * Class AwardLogModel
 * @package app\common\model
 */
class AwardLogModel extends Model
{
    /**
     * 记录奖励
     * @param $uids int|array
     * @param $award
     * @param $type
     * @param $remark
     * @param array|int $order
     * @param string $field
     * @return int
     */
    public static function record($uids,$award,$type,$remark,$order=[],$field='reward'){
        $award=$award*100;
        $datas=[ ];
        $time=time();
        $order_id=0;
        $from_id=0;
        if(!empty($order)) {
            if (is_array($order) || is_object($order)) {
                $order_id = $order['order_id'];
                $from_id = $order['member_id'];
            } else {
                $from_id = intval($order);
            }
        }
        if(!is_array($uids))$uids=idArr($uids);
        $uid_groups=array_chunk($uids,500);
        $count=0;
        foreach ($uid_groups as $uidgroup) {
            foreach ($uidgroup as $uid) {
                $datas[] = [
                    'member_id' => $uid,
                    'order_id' => $order_id,
                    'from_member_id' => $from_id,
                    'type' => $type,
                    'amount' => $award,
                    'real_amount' => $award,
                    'remark' => $remark,
                    'create_time' => $time
                ];
            }

            money_log($uidgroup, $award, $remark, $type, $from_id, $field);

            $count += Db::name('AwardLog')->insertAll($datas);
        }
        return $count;
    }

    public static function rand_award($total,$count,$total_count,$precision=0,$ratio=5,$disperse=100000)
    {
        $min=pow(10,-$precision);
        $keep=($total_count-$count-1)*$min;

        $max=$total-$keep;
        $remain_count=$total_count-$count;
        if($remain_count==1){
            return round($max,$precision);
        }
        $avg=$total/$remain_count;

        $max2=min($avg+$avg-$min,$max);


        $rand=1-pow(mt_rand(1,pow(10,$ratio)),1/$ratio)/10;

        $rand3=$disperse>0?mt_rand(1,$disperse):11;
        if($rand3<10){
            $amount = $max2 + $rand * ($max - $max2);
        }else {
            $rand2 = mt_rand(1, 100);
            if ($rand2 > 50) {
                $amount = $avg + $rand * ($max2 - $avg);
            } else {
                $amount = $avg - $rand * ($avg - $min);
            }
        }
        return round($amount,$precision);

    }
}