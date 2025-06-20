<?php

namespace app\admin\controller;

use app\admin\validate\RegionValidate;
use app\common\facade\RegionFacade;
use Overtrue\Pinyin\Pinyin;
use think\Db;

class RegionController extends BaseController
{

    /**
     * 区域管理
     */
    public function index($keyword = "", $cate_id = 0, $pagesize = 15)
    {
        if ($this->request->isPost()) {
            return redirect(url('', ['cate_id' => $cate_id, 'keyword' => base64url_encode($keyword)]));
        }
        $keyword = empty($keyword) ? "" : base64url_decode($keyword);
        $model = Db::view('region', '*')
            ->view('region category', ['name' => 'category_name', 'title' => 'category_title'], 'region.pid=category.id', 'LEFT')
            ->where('region.pid', '>', 0);
        if (!empty($keyword)) {
            $model->whereLike('region.title|region.title_en', "%$keyword%");
        }
        if ($cate_id > 0) {
            $model->whereIn('region.pid', RegionFacade::getSubCateIds($cate_id, 3));
        }

        $lists = $model->order('id DESC')->paginate($pagesize);

        $this->assign('lists', $lists->items());
        $this->assign('page', $lists->render());
        $this->assign('keyword', $keyword);
        $this->assign('cate_id', $cate_id);
        $this->assign("category", RegionFacade::getSubCategory(0, 3));

        return $this->fetch();
    }

    public function batch($pid = 0)
    {
        $content = $this->request->post('content');
        $rows = explode("\n", $content);
        $datas = [];
        $names = Db::name('region')->field('name')->select();
        $names = array_column($names, 'name');
        $pinyin = new Pinyin();
        $sort = 0;
        if ($pid > 0) {
            $sort = Db::name('region')->where('pid', $pid)->max('sort') + 1;
        } else {
            $sort = Db::name('region')->max('sort') + 1;
        }
        foreach ($rows as $item) {
            $item = trim($item);
            if (empty($item)) continue;
            $fields = explode(',', $item);
            $fieldCount = count($fields);
            $data = ['pid' => $pid, 'sort' => $sort++];
            if ($fieldCount > 2) {
                $data['title'] = trim($fields[0]);
                $data['title_en'] = trim($fields[1]);
                $data['name'] = trim($fields[2]);
            } elseif ($fieldCount > 1) {
                $data['title'] = trim($fields[0]);
                $data['title_en'] = trim($fields[1]);
                $data['name'] = strtolower(str_replace([' ', "'"], ['_', ''], trim($fields[1])));
            } else {
                $data['title'] = trim($fields[0]);
                $data['title_en'] = trim($fields[0]);
                $data['name'] = $pinyin->permalink(trim($fields[0]), '');
            }
            if (in_array($data['name'], $names)) {
                $parts = explode('_', $data['name']);
                $partCount = count($parts);
                if (count($parts) > 1) {
                    $parts[$partCount - 1] += 1;
                    while (((in_array(implode('_', $parts), $names)))) {
                        $parts[$partCount - 1] += 1;
                    }
                } else {
                    $parts[] = 1;
                    while (((in_array(implode('_', $parts), $names)))) {
                        $parts[$partCount] += 1;
                    }
                }
                $data['name'] = implode('_', $parts);
            }
            $names[] = $data['name'];

            $datas[] = $data;
            unset($data);
        }
        if (!empty($datas)) {
            Db::name('region')->insertAll($datas);
            $this->success('添加成功');
        }
        $this->error('未提交数据');
    }

    /**
     * 删除地区
     * @param $id
     */
    public function delete($id)
    {
        $model = Db::name('region');
        $result = $model->whereIn("id", idArr($id))->delete();
        if ($result) {
            user_log($this->mid, 'deleteregion', 1, '删除地区 ' . $id, 'manager');
            $this->success(lang('Delete success!'), url('region/index'));
        } else {
            $this->error(lang('Delete failed!'));
        }
    }

    public function category($id = 0)
    {

        if ($this->request->isPost()) {
            $data = $this->request->post();
            $validate = new RegionValidate();
            $validate->setId($id);

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {

                try {
                    if ($id > 0) {
                        Db::name('region')->where('id', $id)->update($data);
                    } else {
                        Db::name('region')->insert($data);
                    }

                    RegionFacade::clearCache();
                } catch (\Exception $err) {
                    $this->error(lang('Update failed: %s', [$err->getMessage()]));
                }

                $this->success(lang('Update success!'), url('region/index'));
            }
        }
        $model = Db::name('region')->find($id);
        if (empty($model)) {
            $this->error('地区不存在');
        }
        return json(['data' => $model, 'code' => 1]);
    }
}
