<?php

namespace app\api\controller\member;


use app\api\controller\AuthedController;
use app\common\model\MemberFavouriteModel;
use think\Db;

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
    public function index($type)
    {
        $model = new MemberFavouriteModel();
        return $this->response($model->getFavourites($type));
    }

    /**
     * 是否收藏
     */
    public function has($type, $id)
    {
        $model = new MemberFavouriteModel();
        $faved = $model->isFavourite($this->user['id'], $type, $id);
        return $this->response($faved);
    }

    /**
     * 添加到收藏
     * @param string $type product/article
     * @param int|string|array $id 
     * @return void 
     */
    public function add($type, $id)
    {
        $model = new MemberFavouriteModel();
        $ids = idArr($id);
        if (count($ids) > 1) {
            foreach ($ids as $id) {
                $model->addFavourite($this->user['id'], $type, $id);
            }
            $this->success('处理成功');
        }
        if ($model->addFavourite($this->user['id'], $type, $id)) {
            $this->success('已添加收藏');
        } else {
            $this->error($model->getError());
        }
    }

    /**
     * 移出收藏
     * @param mixed $type 
     * @param mixed $ids 
     * @return void 
     */
    public function remove($type, $ids)
    {
        $model = Db::name('memberFavourite')
            ->where('member_id', $this->user['id']);
        if (empty($type)) {
            $model->whereIn('id', idArr($ids));
        } else {
            $model->where('fav_type', $type)
                ->whereIn('fav_id', idArr($ids));
        }
        $model->delete();
        $this->success('已移除收藏');
    }
}
