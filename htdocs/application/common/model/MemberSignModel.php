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
        $time = $this->format_time($time);
        $month = date('Y-m',$time);
        $list = Db::name('signLog')->where('member_id',$member_id)->whereRaw(' INSTR(signdate, \''.$month.'\')==1 ')->order('signdate ASC')->select();
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

    public function sign($member_id, $mood='', $time=NULL, $is_sup=false)
    {
        if($this->settings['open']==0){
            $this->error='签到功能未开启';
            return false;
        }
        $time = $this->format_time($time);
        $date = date('Y-m-d',$time);

        $exists=Db::name('signLog')->where('member_id',$member_id)->where('signdate',$date)->find();
        if(!empty($exists)){
            if($date == date('Y-m-d')){
                $this->error='今天已经签过到了';
            }else{
                $this->error=$date.' 已经签过到了';
            }
            return false;
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

        }
        return $insertid;
    }
}