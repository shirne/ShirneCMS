<?php

namespace app\admin\controller\shop;

use app\admin\controller\BaseController;
use app\admin\model\SpecificationsModel;
use app\admin\validate\ProductCategoryValidate;
use app\common\facade\ProductCategoryFacade;
use app\common\model\ProductCategoryModel;
use Overtrue\Pinyin\Pinyin;
use think\Db;

/**
 * 商品分类
 * Class CategoryController
 * @package app\admin\controller\shop
 */
class CategoryController extends BaseController
{
    /**
     * 分类列表
     * @return mixed
     */
    public function index()
    {
        $this->assign('model', ProductCategoryFacade::getCategories());
        return $this->fetch();
    }

    /**
     * 添加
     * @param int $pid
     * @return mixed
     */
    public function add($pid = 0)
    {
        $pid = intval($pid);
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $validate = new ProductCategoryValidate();
            $validate->setId();

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                $iconupload = $this->_upload('category', 'upload_icon');
                if (!empty($iconupload)) $data['icon'] = $iconupload['url'];
                elseif ($this->uploadErrorCode > 102) {
                    $this->error($this->uploadErrorCode . ':' . $this->uploadError);
                }
                $uploaded = $this->_upload('category', 'upload_image');
                if (!empty($uploaded)) $data['image'] = $uploaded['url'];
                elseif ($this->uploadErrorCode > 102) {
                    delete_image($data['icon']);
                    $this->error($this->uploadErrorCode . ':' . $this->uploadError);
                }

                $model = ProductCategoryModel::create($data);
                if ($model['id']) {
                    ProductCategoryFacade::clearCache();
                    $this->success(lang('Add success!'), murl('shop.category/index'));
                } else {
                    $this->error(lang('Add failed!'));
                }
            }
        }
        $cate = ProductCategoryFacade::getCategories();
        $model = array('sort' => 99, 'pid' => $pid, 'specs' => [], 'is_hot' => 0);
        $this->assign('cate', $cate);
        $this->assign('model', $model);
        $this->assign('specs', SpecificationsModel::getList());
        $this->assign('id', 0);
        return $this->fetch('edit');
    }

    /**
     * 修改
     * @param $id
     * @return mixed
     */
    public function edit($id)
    {
        $id = intval($id);
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $validate = new ProductCategoryValidate();
            $validate->setId($id);

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                $delete_images = [];
                $iconupload = $this->_upload('category', 'upload_icon');
                if (!empty($iconupload)) {
                    $data['icon'] = $iconupload['url'];
                    $delete_images[] = $data['delete_icon'];
                } elseif ($this->uploadErrorCode > 102) {
                    $this->error($this->uploadErrorCode . ':' . $this->uploadError);
                }
                $uploaded = $this->_upload('category', 'upload_image');
                if (!empty($uploaded)) {
                    $data['image'] = $uploaded['url'];
                    $delete_images[] = $data['delete_image'];
                } elseif ($this->uploadErrorCode > 102) {
                    delete_image($data['icon']);
                    $this->error($this->uploadErrorCode . ':' . $this->uploadError);
                }
                unset($data['delete_icon']);
                unset($data['delete_image']);
                if (empty($data['specs'])) $data['specs'] = [];

                try {
                    ProductCategoryModel::update($data, ['id' => $id]);

                    delete_image($delete_images);
                    ProductCategoryFacade::clearCache();
                } catch (\Exception $err) {
                    $this->error(lang('Update failed: %', [$err->getMessage()]));
                }
                $this->success(lang('Update success!'), murl('shop.category/index'));
            }
        }

        $model = ProductCategoryModel::get($id);
        if (empty($model) || empty($model['id'])) {
            $this->error('分类不存在');
        }
        $cate = ProductCategoryFacade::getCategories();
        if (is_null($model->specs)) {
            $model->specs = [];
        }

        $this->assign('cate', $cate);
        $this->assign('model', $model);
        $this->assign('specs', SpecificationsModel::getList());
        $this->assign('id', $id);
        return $this->fetch();
    }

    public function batch($pid = 0)
    {
        $content = $this->request->post('content');
        $rows = explode("\n", $content);
        $datas = [];
        $names = Db::name('productCategory')->field('name')->select();
        $names = array_column($names, 'name');
        $pinyin = new Pinyin();
        $sort = 0;
        if ($pid > 0) {
            $sort = Db::name('productCategory')->where('pid', $pid)->max('sort') + 1;
        } else {
            $sort = Db::name('productCategory')->max('sort') + 1;
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
            Db::name('productCategory')->insertAll($datas);
            $this->success('添加成功');
        }
        $this->error('未提交数据');
    }

    public function status($id, $status = 0)
    {
        $data['status'] = $status == 1 ? 1 : 0;
        $updated = Db::name('ProductCategory')->whereIn('id', idArr($id))->where('is_lock', 0)->update($data);
        if (!$updated) {
            $this->error('更新失败');
        }
        $this->success('更新成功');
    }

    public function lock($id)
    {
        $updated = Db::name('ProductCategory')->whereIn('id', idArr($id))->where('is_lock', 0)->update(['is_lock' => 1]);
        if (!$updated) {
            $this->error('更新失败');
        }
        $this->success('锁定成功');
    }

    public function unlock($id)
    {
        $updated = Db::name('ProductCategory')->whereIn('id', idArr($id))->where('is_lock', 1)->update(['is_lock' => 0]);
        if (!$updated) {
            $this->error('更新失败');
        }
        $this->success('解锁成功');
    }

    /**
     * 删除
     * @param $id
     */
    public function delete($id)
    {
        $id = idArr($id);
        //查询属于这个分类的文章
        $posts = Db::name('Product')->whereIn('cate_id', $id)->count();
        if ($posts) {
            $this->error("禁止删除含有产品的分类");
        }
        //禁止删除含有子分类的分类
        $hasChild = Db::name('ProductCategory')->whereIn('pid', $id)->count();
        if ($hasChild) {
            $this->error("禁止删除含有子分类的分类");
        }
        //验证通过
        $result = Db::name('ProductCategory')->whereIn('id', $id)->where('is_lock', 0)->delete();
        if ($result) {
            ProductCategoryFacade::clearCache();
            $this->success(lang('Delete success!'), murl('shop.category/index'));
        } else {
            $this->error(lang('Delete failed!'));
        }
    }
}
