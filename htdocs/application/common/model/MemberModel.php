<?php
namespace app\common\model;

use think\Db;

/**
 * Class MemberModel
 * @package app\common\model
 */
class MemberModel extends BaseModel
{

    protected $autoWriteTimestamp = true;

    public static function init()
    {
        parent::init();
        self::afterUpdate(function ($model) {
            $users=$model->where($model->getWhere())->select();
            //代理会员组
            if(!empty($users)) {
                $levels = getMemberLevels();
                foreach ($users as $user) {
                    //代理会员组
                    if (!$user['is_agent'] && $user['level_id'] > 0) {
                        if (!empty($levels[$user['level_id']]) && $levels[$user['level_id']]['is_agent']) {
                            if(self::setAgent($model->id)){
                                self::updateRecommend($model['referer']);
                            }
                        }
                    }
                }
            }
        });
        self::afterInsert(function ( $model) {
            if ($model['referer']) {
                Db::name('member')->where('id',$model->referer)->setInc('recom_total',1);
            }
            if ($model['level_id']) {
                $levels = getMemberLevels();
                if (!$model['is_agent'] ) {
                    if (!empty($levels[$model['level_id']]) && $levels[$model['level_id']]['is_agent']) {
                        if(self::setAgent($model->id)){
                            self::updateRecommend($model['referer']);
                        }
                    }
                }
            }
        });
    }

    public static function updateRecommend($referer){
        if($referer){
            Db::name('member')->where('id',$referer)->setInc('recom_count',1);
            $parents=getMemberParents($referer,0);
            array_unshift($parents,$referer);
            Db::name('member')->whereIn('id',$parents)->setInc('team_count',1);

            //代理等级自动升级

        }
    }

    public static function setAgent($member_id){
        $data=array();
        $data['agentcode']=random_str(8);
        while(Db::name('member')->find(['agentcode'=>$data['agentcode']])){
            $data['agentcode']=random_str(8);
        }
        $data['is_agent']=1;
        return Db::name('member')->where('id',$member_id)->update($data);
    }
    public static function cancelAgent($member_id){
        $data=array();
        $data['is_agent']=0;
        $count= Db::name('member')->where('id',$member_id)->update($data);
        if($count){
            $parents=getMemberParents($member_id,0);
            Db::name('member')->where('id',$parents[0])->setDec('recom_count',1);
            Db::name('member')->whereIn('id',$parents)->setDec('team_count',1);
        }
        return $count;
    }
}