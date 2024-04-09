<?php

/**
 * 商品分类
 * User: shirne
 * Date: 2018/5/11
 * Time: 17:48
 */

namespace app\admin\controller\credit;

use app\admin\controller\BaseController;
use app\admin\model\SpecificationsModel;
use app\admin\validate\GoodsCategoryValidate;
use app\common\facade\GoodsCategoryFacade;
use app\common\model\GoodsCategoryModel;
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
        $this->assign('specs', SpecificationsModel::getList());
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
                    $delete_images[] = $data['delete_icon'];
                }
                $uploaded = $this->_upload('category', 'upload_image');
                if (!empty($uploaded)) {
                    $data['image'] = $uploaded['url'];
                    $delete_images[] = $data['delete_image'];
                }
                unset($data['delete_icon']);
                unset($data['delete_image']);

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
        $this->assign('specs', SpecificationsModel::getList());
        $this->assign('id', $id);
        return $this->fetch();
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
