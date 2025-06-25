<?php


namespace app\admin\controller\shop;

use app\admin\controller\BaseController;
use app\admin\validate\PostageValidate;
use app\common\facade\RegionFacade;
use app\common\model\PostageModel;
use app\common\model\RegionModel;
use think\Db;

class PostageController extends BaseController
{
    /**
     * 运费模板列表
     */
    public function index($store_id = '')
    {
        $model = PostageModel::order('id ASC');
        if ($store_id !== '') {
            $store_id = intval($store_id);

            $model->where('store_id', $store_id);
        }
        $paged = $model->paginate();
        $lists = $paged->items();
        $factids = array_column($lists, 'store_id');
        if (!empty($factids)) {
            $factories = Db::name('factory')->whereIn('id', $factids)->select();
            if (!empty($factories)) {
                $factories = array_column($factories, NULL, 'id');
                foreach ($lists as &$item) {
                    if (isset($factories[$item['store_id']])) {
                        $item['store_name'] = $factories[$item['store_id']]['title'];
                    }
                }
                unset($item);
            }
        }
        foreach ($lists as &$item) {
            if (!empty($item['regions'])) {
                $item['region_names'] = implode(',', RegionModel::GetTitles($item['regions']));
            } else {
                $item['region_names'] = '';
            }
            if (!empty($item['specials'])) {
                $item['special_names'] = implode(',', RegionModel::GetTitles($item['specials']));
            } else {
                $item['special_names'] = '';
            }
        }
        unset($item);
        $this->assign('lists', $lists);
        $this->assign('page', $paged->render());
        $this->assign('store_id', $store_id);
        return $this->fetch();
    }

    /**
     * 添加邮费模板
     */
    public function add()
    {
        if ($this->request->isPost()) {

            $data = $this->request->post();
            $validate = new PostageValidate();

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                $areas = $data['areas'];
                unset($data['areas']);
                if (empty($data['specials'])) $data['specials'] = [];
                if (!empty($data['regions'])) {
                    $datas = RegionModel::GetTitles($data['regions']);
                    if (count($datas) > 1) $data['country'] = $datas[1];
                    if (count($datas) > 2) $data['province'] = $datas[2];
                    if (count($datas) > 3) $data['city'] = $datas[3];
                }
                $levelModel = PostageModel::create($data);
                $insertId = $levelModel['id'];
                if ($insertId !== false) {
                    PostageModel::updateAreas($areas, $insertId);
                    cache('postage', null);
                    user_log($this->mid, 'addpostage', 1, '添加运费模板' . $insertId, 'manager');
                    $this->success(lang('Add success!'), url('shop.postage/index'));
                } else {
                    $this->error(lang('Add failed!'));
                }
            }
        }
        $counts = Db::name('postage')->where('is_default', 1)->count();
        $this->assign('model', [
            'is_default' => $counts < 1 ? 1 : 0,
            'area_type' => 0,
            'calc_type' => 0,
            'specials' => [],
        ]);
        $this->assign('areas', [
            ['id' => 0, 'sort' => 0]
        ]);
        $this->assign('region_names', '');
        $this->assign('express', config('express.'));
        return $this->fetch('update');
    }

    /**
     * 修改邮费设置
     */
    public function update($id)
    {
        $id = intval($id);
        $model = PostageModel::get($id);
        if (empty($model)) {
            $this->error('运费模板不存在');
        }
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $validate = new PostageValidate();

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                if (empty($data['specials'])) $data['specials'] = [];
                if (!empty($data['regions'])) {
                    $datas = RegionModel::GetTitles($data['regions']);
                    if (count($datas) > 1) $data['country'] = $datas[1];
                    if (count($datas) > 2) $data['province'] = $datas[2];
                    if (count($datas) > 3) $data['city'] = $datas[3];
                }
                if ($model->allowField(true)->save($data)) {
                    PostageModel::updateAreas($data['areas'], $id);
                    cache('postage', null);
                    user_log($this->mid, 'updatepostage', 1, '修改运费模板' . $id, 'manager');
                    $this->success(lang('Update success!'), url('shop.postage/index'));
                } else {
                    $this->error(lang('Update failed!'));
                }
            }
        }
        $model->specials = RegionFacade::findCategories($model->specials);
        $this->assign('model', $model);
        $this->assign('region_names', implode(',', RegionModel::GetTitles($model['regions'])));
        $this->assign('areas', $model->getAreas());
        $this->assign('express', config('express.'));
        return $this->fetch();
    }

    /**
     * 删除邮费设置
     * @param $id
     */
    public function delete($id)
    {
        $id = intval($id);
        $count = Db::name('product')->where('postage_id', $id)->count();
        if ($count > 0) {
            $this->error("该模板尚有产品使用,不能删除");
        }
        $result = Db::name('postage')->where('id', $id)->delete();
        if ($result) {
            Db::name('postageArea')->where('postage_id', $id)->delete();
            cache('postage', null);
            $this->success(lang('Delete success!'), url('shop.postage/index'));
        } else {
            $this->error(lang('Delete failed!'));
        }
    }
}
