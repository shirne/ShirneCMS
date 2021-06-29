<?php

namespace app\api\controller\member;


use app\api\controller\AuthedController;
use app\common\model\MemberFavouriteModel;
use think\facade\Db;

/**
 * 会员收藏管理
 * @package app\api\controller\member
 */
class FavouriteController extends AuthedController
{
    /**
     * 获取收藏列表
     * @param mixed $type 
     * @return void 
     */
    public function index($type){
        $model=new MemberFavouriteModel();
        $this->response($model->getFavourites($type));
    }

    /**
     * 添加到收藏
     * @param mixed $type 
     * @param mixed $id 
     * @return void 
     */
    public function add($type,$id){
        $model=new MemberFavouriteModel();
        if($model->addFavourite($this->user['id'],$type,$id)){
            $this->success('已添加收藏');
        }else{
            $this->error($model->getError());
        }
    }

    /**
     * 移出收藏
     * @param mixed $type 
     * @param mixed $ids 
     * @return void 
     */
    public function remove($type,$ids){
        $model=Db::name('memberFavourite')
        ->where('member_id',$this->user['id']);
        if(empty($type)){
            $model->whereIn('id',idArr($ids));
        }else{
            $model->where('fav_type',$type)
            ->whereIn('fav_id',idArr($ids));
        }
        $model->delete();
        $this->success('已移除收藏');
    }
}