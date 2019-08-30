<?php


namespace app\common\model;

use think\Db;

class MemberSignModel extends BaseModel
{
    protected $settings=[
        'open'=>0
    ];
    public function __construct($data = [])
    {
        parent::__construct($data);

        //初始化配置
        $settings = getSettings(false,true);
        if(!empty($settings['sign'])){
            foreach($settings['sign'] as $k=>$v){
                $key = $k;
                if(strpos($k,'sign_')===0)$key = substr($k,5);
                $this->settings[$key]=$v;
            }
            if(!empty($this->settings['keep_award'])){
                usort($this->settings['keep_award'],function ($prev, $next){
                    if($prev['day'] == $next['day'])return 0;
                    return $prev['day'] > $next['day']?1:-1;
                });
            }
        }
    }

    protected function format_time($time)
    {
        if(is_null($time)){
            return time();
        }
        if(!is_numeric($time)){
            $time = strtotime($time);
        }
        return intval($time);
    }

    public function getSigns($member_id, $time = NULL)
    {
        $model = Db::name('signLog')->where('member_id',$member_id);
        if(is_array($time)){
            $start=$this->format_time($time[0]);
            if(isset($time[1])){
                $end=$this->format_time($time[1]);
                $model->whereBetween('signdate',[date('Y-m-d',$start),date('Y-m-d',$end)]);
            }else{
                $model->where('signdate','>=', date('Y-m-d',$start));
            }
        }else{
            $time = $this->format_time($time);
            $month = date('Y-m',$time);
            $model->whereRaw(' INSTR(signdate, \''.$month.'\') = 1 ');
        }
        $list = $model->order('signdate ASC')->select();
        return array_column($list, NULL, 'signdate');
    }

    public function getSignRank($time, $num = 10)
    {
        $time = $this->format_time($time);
        $model = Db::view('signLog','*')
        ->view('member','username,nickname,level_id,avatar,gender,status,delete_time','member.id = signLog.member_id','LEFT')
        ->where('signdate',date('Y-m-d',$time));

        $list = $model->order('ranking_day ASC')->limit($num)->select();
        return $list;
    }

    public function getLastSign($member_id)
    {
        return Db::name('signLog')->where('member_id',$member_id)->order('signdate DESC')->find();
    }

    public function sign($member_id, $mood='', $time=NULL, $is_sup=false)
    {
        if($this->settings['open']==0){
            $this->error='签到功能未开启';
            return false;
        }
        $time = $this->format_time($time);
        if($time>=strtotime('today +1 day')){
            $this->error='还没到签到时间哦~';
            return false;
        }
        if($time<strtotime('today')){
            $is_sup=true;
        }
        $date = date('Y-m-d',$time);
        if($is_sup){
            $today = Db::name('signLog')->where('member_id',$member_id)->where('signdate',date('Y-m-d'))->find();
            if(empty($today)){
                $this->error='今天还没签到哦~';
                return false;
            }
        }

        $exists=Db::name('signLog')->where('member_id',$member_id)->where('signdate',$date)->find();
        if(!empty($exists)){
            if($date == date('Y-m-d')){
                $this->error='今天已经签过到了';
            }else{
                $this->error=$date.' 已经签过到了';
            }
            return false;
        }
        if($is_sup){
            if(!$this->settings['sup_sign_open']){
                $this->error='系统未开启补签';
                return false;
            }
            if(empty($this->settings['sup_sign_rule']['times'])){
                $this->error='本月补签次数已用完';
                return false;
            }
            $supcount = Db::name('signLog')->where('member_id',$member_id)
                ->where('is_sup',1)
                ->where('signdate','>=',date('Y-m-01'))->count();
            if($supcount>=$this->settings['sup_sign_rule']['times']){
                $this->error='本月补签次数已用完';
                return false;
            }
        }
        $count = Db::name('signLog')->where('signdate',$date)->count();

        $keep_days=1;
        if(date('d') != '01' || $this->settings['cycle']!='month'){
            $lastdate = date('Y-m-d',strtotime($date.' -1 day'));
            $lastsign = Db::name('signLog')->where('member_id',$member_id)->where('signdate',$lastdate)->find();
            if(!empty($lastsign )){
                $keep_days = $lastsign['keep_days']+1;
            }
        }
        $insertid = Db::name('signLog')->insert([
            'member_id'=>$member_id,
            'is_sup'=>$is_sup,
            'ranking_day' =>$count+1,
            'keep_days' =>$keep_days,
            'signdate' =>$date,
            'signtime' =>time(),
            'mood' =>$mood,
            'remark' =>'',
        ],false,true);
        if($insertid){
            $returnmsg='签到成功';
            $awarded=false;
            if($is_sup){
                $credit = empty($this->settings['sup_sign_rule']['credit'])?0 :intval($this->settings['sup_sign_rule']['credit']);
                money_log($member_id,-$credit*100,'补签扣除积分','sign',0,'credit');
                
                $today=strtotime('today');
                $nextday = strtotime($date.' +1 day');
                while($nextday<=$today){
                    $signed = Db::name('signLog')->where('member_id',$member_id)->where('signdate',date('Y-m-d',$nextday))->find();
                    if(empty($signed)){
                        break;
                    }
                    $nextday = strtotime(date('Y-m-d',$nextday).' +1 day');
                }
                Db::name('signLog')->where('member_id',$member_id)->where('signdate','>',$date)
                    ->where('signdate','<',date('Y-m-d',$nextday))->update(['keep_days'=>['INC',1]]);
            }elseif(!empty($this->settings['keep_award'])) {
                //是否有连接签到奖励
                $days = array_column($this->settings['keep_award'],'day');
                $maxday = max($days);
                $cdays=$keep_days%$maxday;
                $rkey=-1;
                foreach ($days as $key=>$day){
                    if($cdays < $day)break;
                    if($cdays == $day)$rkey = $key;
                }
                if($rkey > -1){
                    $credit = floatval($this->settings['keep_award'][$rkey]['value']);
                    if($credit>0) {
                        $awarded=true;
                        $returnmsg = '连续签到' . $days[$rkey] . '天奖励'.$credit.'积分';
                        money_log($member_id, $credit*100, '连续签到' . $days[$rkey] . '天奖励', 'sign', 0, 'credit');
                    }
                }
            }
            
            if(!$awarded){
                $credit = empty($this->settings['award']['first'])?0:floatval($this->settings['award']['first']);
                if(!empty($credit) && !$is_sup){
                    $isfirst = 0;
                    if($keep_days==1){
                        if( $this->settings['cycle']=='month'){
                            $isfirst = Db::name('signLog')->where('member_id',$member_id)
                                ->where('signdate','>=',date('Y-m-01',time()))->count();
                        }else{
                            $isfirst = Db::name('signLog')->where('member_id',$member_id)->count();
                        }
                    }
                    if($isfirst==1){
                        $awarded=true;
                        $returnmsg = '首次签到奖励'.$credit.'积分';
                        money_log($member_id, $credit*100, '首次签到奖励', 'sign', 0, 'credit');
                    }
                }
    
                $credit = empty($this->settings['award']['normal'])?0:floatval($this->settings['award']['normal']);
                if(!$awarded && !empty($credit)){
                    $returnmsg = '签到成功,奖励'.$credit.'积分';
                    money_log($member_id, $credit*100, '日常签到奖励', 'sign', 0, 'credit');
                }
            }
            
            $this->setError($returnmsg,0);
        }
        return $insertid;
    }
}