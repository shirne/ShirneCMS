<?php

namespace app\index\controller\member;

use app\common\model\MemberFavouriteModel;
use think\Db;

/**
 * 收藏控制器
 * Class FavouriteController
 * @package app\index\controller\member
 */
class FavouriteController extends BaseController
{
    public function index($type = '')
    {
        if ($this->request->isPost()) {
        }
        $model = Db::name('memberFavourite')->where('member_id', $this->userid);
        if (!empty($type)) {
            $model->where('fav_type', $type);
        }
        $favs = $model->select();
        $this->assign('favs', $favs);
        return $this->fetch();
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

    public function delete($ids = '', $invalid = '')
    {
        if (!empty($ids)) {
            Db::name('memberFavourite')->where('member_id', $this->userid)
                ->whereIn('id', idArr($ids))
                ->delete();
        } elseif ($invalid !== '') {
            $inSql = Db::name('product')->where('status', 1)->field('id')->select(true);
            Db::name('memberFavourite')->where('member_id', $this->userid)
                ->where('fav_type', 'product')
                ->whereNotIn('fav_id', $inSql)
                ->delete();
        }
        $this->success('取消收藏成功');
    }
}
