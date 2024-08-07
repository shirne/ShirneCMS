<?php

/**
 * 商品分类
 * User: shirne
 * Date: 2018/5/11
 * Time: 17:48
 */

namespace app\admin\controller\credit;

use app\admin\controller\BaseController;
use modules\credit\validate\GoodsCategoryValidate;
use modules\credit\facade\GoodsCategoryFacade;
use modules\credit\model\GoodsCategoryModel;
use Overtrue\Pinyin\Pinyin;
use think\Db;

class CategoryController extends BaseController
{
    public function index()
    {
        $this->assign('lists', GoodsCategoryFacade::getCategories(true));
        return $this->fetch();
    }
    public function add($pid = 0)
    {
        $pid = intval($pid);
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $validate = new GoodsCategoryValidate();
            $validate->setId();

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                $iconupload = $this->_upload('category', 'upload_icon');
                if (!empty($iconupload)) $data['icon'] = $iconupload['url'];
                $uploaded = $this->_upload('category', 'upload_image');
                if (!empty($uploaded)) $data['image'] = $uploaded['url'];

                $model = GoodsCategoryModel::create($data);
                if ($model['id']) {
                    GoodsCategoryFacade::clearCache();
                    $this->success("添加成功", url('credit.category/index'));
                } else {
                    $this->error("添加失败");
                }
            }
        }
        $cate = GoodsCategoryFacade::getCategories();
        $model = array('sort' => 99, 'pid' => $pid, 'specs' => []);
        $this->assign('cate', $cate);
        $this->assign('model', $model);
        $this->assign('id', 0);
        return $this->fetch('edit');
    }

    /**
     * 编辑分类
     */
    public function edit($id)
    {
        $id = intval($id);
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $validate = new GoodsCategoryValidate();
            $validate->setId($id);

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                $delete_images = [];
                $iconupload = $this->_upload('category', 'upload_icon');
                if (!empty($iconupload)) {
                    $data['icon'] = $iconupload['url'];
                    if (!empty($data['delete_icon'])) $delete_images[] = $data['delete_icon'];
                }
                $uploaded = $this->_upload('category', 'upload_image');
                if (!empty($uploaded)) {
                    $data['image'] = $uploaded['url'];
                    if (!empty($data['delete_image'])) $delete_images[] = $data['delete_image'];
                }
                if (isset($data['delete_icon'])) unset($data['delete_icon']);
                if (isset($data['delete_image'])) unset($data['delete_image']);

                GoodsCategoryModel::update($data, ['id' => $id]);

                delete_image($delete_images);
                GoodsCategoryFacade::clearCache();
                $this->success("保存成功", url('credit.category/index'));
            }
        }
        $model = GoodsCategoryModel::get($id);
        if (empty($model) || empty($model['id'])) {
            $this->error('分类不存在');
        }
        $cate = GoodsCategoryFacade::getCategories();
        if (is_null($model->specs)) {
            $model->specs = [];
        }

        $this->assign('cate', $cate);
        $this->assign('model', $model);
        $this->assign('id', $id);
        return $this->fetch();
    }



    public function batch($pid = 0)
    {
        $content = $this->request->post('content');
        $rows = explode("\n", $content);
        $datas = [];
        $names = Db::name('goodsCategory')->field('name')->select();
        $names = array_column($names, 'name');
        $pinyin = new Pinyin();

        $sort = 0;
        if ($pid > 0) {
            $sort = Db::name('goodsCategory')->where('pid', $pid)->max('sort') + 1;
        } else {
            $sort = Db::name('goodsCategory')->max('sort') + 1;
        }
        foreach ($rows as $item) {
            $item = trim($item);
            if (empty($item)) continue;
            $fields = explode(' ', $item);
            $fieldCount = count($fields);
            $data = ['pid' => $pid, 'sort' => $sort++];
            if ($fieldCount > 2) {
                $data['title'] = trim($fields[0]);
                $data['short'] = trim($fields[1]);
                $data['name'] = trim($fields[2]);
            } elseif ($fieldCount > 1) {
                $data['title'] = trim($fields[0]);
                $data['short'] = trim($fields[0]);
                $data['name'] = trim($fields[1]);
            } else {
                $data['title'] = trim($fields[0]);
                $data['short'] = trim($fields[0]);
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
            Db::name('goodsCategory')->insertAll($datas);
            $this->success('添加成功');
        }
        $this->error('未提交数据');
    }

    /**
     * 删除分类
     */
    public function delete($id)
    {
        $id = idArr($id);
        //查询属于这个分类的文章
        $posts = Db::name('Goods')->where('cate_id', 'in', $id)->count();
        if ($posts) {
            $this->error("禁止删除含有产品的分类");
        }
        //禁止删除含有子分类的分类
        $hasChild = Db::name('GoodsCategory')->where('pid', 'in', $id)->count();
        if ($hasChild) {
            $this->error("禁止删除含有子分类的分类");
        }
        //验证通过
        $result = Db::name('GoodsCategory')->where('id', 'in', $id)->delete();
        if ($result) {
            GoodsCategoryFacade::clearCache();
            $this->success("分类删除成功", url('credit.category/index'));
        } else {
            $this->error("分类删除失败");
        }
    }
}
