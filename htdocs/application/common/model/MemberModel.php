<?php
namespace app\common\model;

use think\Db;
use think\Model;

class MemberModel extends Model
{

    protected $autoWriteTimestamp = true;

    public static function init()
    {
        parent::init();
        self::afterUpdate(function ($model) {
            $users=$model->where($model->getWhere())->find();
            //代理会员组
            if(!empty($users)) {
                $levels = getMemberLevels();
                foreach ($users as $user) {
                    //代理会员组
                    if (!$user['is_agent'] && $user['level_id'] > 0) {
                        if (!empty($levels[$user['level_id']]) && $levels[$user['level_id']]['is_agent']) {
                            self::setAgent($user->id);
                        }
                    }
                }
            }
        });
        self::afterInsert(function ( $model) {
            if ($user['referer']) {
                Db::name('member')->where(array('id'=>$user->referer))->setInc('total_recommend',1);
            }
        });
    }

    public static function setAgent($member_id){
        $data=array();
        $data['agentcode']=random_str(8);
        while(Db::name('member')->find(['agentcode'=>$data['agentcode']])){
            $data['agentcode']=random_str(8);
        }
        $data['is_agent']=1;
        return Db::name('member')->where(array('id'=>$member_id))->update($data);
    }
    public static function cancelAgent($member_id){
        $data=array();
        $data['is_agent']=0;
        return Db::name('member')->where(array('id'=>$member_id))->update($data);
    }
}