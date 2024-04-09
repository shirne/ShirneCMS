<?php

namespace app\admin\controller;

use app\admin\model\PageModel;
use app\admin\validate\PageGroupValidate;
use app\admin\validate\PageValidate;
use app\admin\validate\ImagesValidate;
use think\Db;

/**
 * 单页管理
 * Class PageController
 * @package app\admin\controller
 */
class PageController extends BaseController
{
    /**
     * 单页列表
     * @param string $key
     * @param string $group
     * @return mixed|\think\response\Redirect
     */
    public function index($key = "", $group = '')
    {
        if ($this->request->isPost()) {
            return redirect(url('', ['group' => $group, 'key' => base64url_encode($key)]));
        }
        $key = empty($key) ? "" : base64url_decode($key);
        $model = Db::view('page', '*');
        if (!empty($key)) {
            $model->whereLike('page.title|page.name|page.group', "%$key%");
        }
        if (!empty($group)) {
            $model->where('group', $group);
        }
        $lists = $model->view('pageGroup', ['group_name', 'use_template' => 'group_use_template'], 'pageGroup.group=page.group', 'LEFT')
            ->order('sort ASC,ID DESC')->paginate(15);
        $this->assign('lists', $lists);
        $this->assign('page', $lists->render());
        $this->assign('group', $group);
        $this->assign('keyword', $key);
        $this->assign('groups', getPageGroups());
        return $this->fetch();
    }

    /**
     * 添加单页
     * @return mixed
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $validate = new PageValidate();
            $validate->setId();
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                $uploaded = $this->_upload('page', 'upload_icon');
                if (!empty($uploaded)) {
                    $data['icon'] = $uploaded['url'];
                } elseif ($this->uploadErrorCode > 102) {
                    $this->error($this->uploadErrorCode . ':' . $this->uploadError);
                }
                $model = PageModel::create($data);
                if ($model['id']) {
                    $this->success(lang('Add success!'), url('page/index'));
                } else {
                    delete_image($data['icon']);
                    $this->error(lang('Add failed!'));
                }
            }
        }
        $model = array('status' => 1);
        $this->assign('page', $model);
        $this->assign('groups', getPageGroups());
        $this->assign('id', 0);
        return $this->fetch('edit');
    }

    /**
     * 修改单页
     * @param $id
     * @return mixed
     */
    public function edit($id)
    {
        $id = intval($id);
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $validate = new PageValidate();
            $validate->setId($id);
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                $model = PageModel::get($id);
                $delete_images = [];
                $uploaded = $this->_upload('page', 'upload_icon');
                if (!empty($uploaded)) {
                    $data['icon'] = $uploaded['url'];
                    $delete_images[] = $data['delete_icon'];
                } elseif ($this->uploadErrorCode > 102) {
                    $this->error($this->uploadErrorCode . ':' . $this->uploadError);
                }
                if ($model->allowField(true)->save($data)) {
                    delete_image($delete_images);
                    $this->success(lang('Update success!'), url('page/index'));
                } else {
                    delete_image($data['icon']);
                    $this->error(lang('Update failed!'));
                }
            }
        }
        $model = Db::name('page')->where('id', $id)->find();
        if (empty($model)) {
            $this->error('要编辑的内容不存在');
        }
        $this->assign('page', $model);
        $this->assign('groups', getPageGroups());
        $this->assign('id', $id);
        return $this->fetch();
    }

    /**
     * 状态设置
     * @param $id
     * @param int $status
     */
    public function status($id, $status = 0)
    {
        $model = Db::name('page');
        $result = $model->where('id', 'in', idArr($id))->update(['status' => intval($status)]);
        if ($result) {
            $this->success("设置成功", url('page/index'));
        } else {
            $this->error("设置失败");
        }
    }

    /**
     * 删除单页
     * @param $id
     */
    public function delete($id)
    {
        $model = Db::name('page');
        $result = $model->where('id', 'in', idArr($id))->delete();
        if ($result) {
            $this->success(lang('Delete success!'), url('page/index'));
        } else {
            $this->error(lang('Delete failed!'));
        }
    }

    /**
     * 图集
     * @param $aid
     * @return mixed
     */
    public function imagelist($aid)
    {
        $model = Db::name('PageImages');
        $page = Db::name('Page')->find($aid);
        if (empty($page)) {
            $this->error('页面不存在');
        }
        $where = array('page_id' => $aid);
        if (!empty($key)) {
            $where[] = array('title', 'like', "%$key%");
        }
        $lists = $model->where($where)->order('sort ASC,id DESC')->paginate(15);
        $this->assign('page', $page);
        $this->assign('lists', $lists);
        $this->assign('page', $lists->render());
        $this->assign('aid', $aid);
        return $this->fetch();
    }

    /**
     * 添加图片
     * @param $aid
     * @return mixed
     */
    public function imageadd($aid)
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $validate = new ImagesValidate();

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                $uploaded = $this->_upload('page', 'upload_image');
                if (!empty($uploaded)) {
                    $data['image'] = $uploaded['url'];
                }
                $model = Db::name("PageImages");
                $url = url('page/imagelist', array('aid' => $aid));
                if ($model->insert($data)) {
                    $this->success(lang('Add success!'), $url);
                } else {
                    delete_image($data['image']);
                    $this->error(lang('Add failed!'));
                }
            }
        }
        $model = array('status' => 1, 'page_id' => $aid);
        $this->assign('aid', $aid);
        $this->assign('model', $model);
        $this->assign('id', 0);
        return $this->fetch('imageupdate');
    }

    /**
     * 修改图片
     * @param $id
     * @return mixed
     */
    public function imageupdate($id)
    {
        $id = intval($id);

        if ($this->request->isPost()) {
            $data = $this->request->post();
            $validate = new ImagesValidate();

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                $model = Db::name("PageImages");
                $url = url('page/imagelist', array('aid' => $data['article_id']));
                $delete_images = [];
                $uploaded = $this->_upload('page', 'upload_image');
                if (!empty($uploaded)) {
                    $data['image'] = $uploaded['url'];
                    $delete_images[] = $data['delete_image'];
                }
                unset($data['delete_image']);
                $data['id'] = $id;
                if ($model->update($data)) {
                    delete_image($delete_images);
                    $this->success(lang('Update success!'), $url);
                } else {
                    delete_image($data['image']);
                    $this->error(lang('Update failed!'));
                }
            }
        } else {
            $model = Db::name('PageImages')->where('id', $id)->find();
            if (empty($model)) {
                $this->error('图片不存在');
            }

            $this->assign('model', $model);
            $this->assign('aid', $model['page_id']);
            $this->assign('id', $id);
            return $this->fetch();
        }
    }

    /**
     * 删除图片
     * @param $aid
     * @param $id
     */
    public function imagedelete($aid, $id)
    {
        $id = intval($id);
        $model = Db::name('PageImages');
        $result = $model->delete($id);
        if ($result) {
            $this->success(lang('Delete success!'), url('page/imagelist', array('aid' => $aid)));
        } else {
            $this->error(lang('Delete failed!'));
        }
    }

    /**
     * 分组管理
     * @return mixed
     */
    public function groups()
    {
        $groups = getPageGroups(true);

        $this->assign('lists', $groups);
        return $this->fetch();
    }

    /**
     * 编辑分组
     * @param int $id
     * @return mixed
     */
    public function groupedit($id = 0)
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $validate = new PageGroupValidate();
            $validate->setId($id);
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }
            if ($id > 0) {
                Db::name('PageGroup')->where('id', $id)->update($data);
                cache('page_group', null);
                $this->success('保存成功', url('page/groups'));
            } else {
                Db::name('PageGroup')->where('id', $id)->insert($data);
                cache('page_group', null);
                $this->success('添加成功', url('page/groups'));
            }
        }
        if ($id > 0) {
            $model = Db::name('PageGroup')->find($id);
        } else {
            $model = array('sort' => 99);
        }
        $this->assign('model', $model);
        $this->assign('id', $id);
        return $this->fetch();
    }

    /**
     * 删除分组
     * @param $id
     */
    public function groupdelete($id)
    {
        $id = idArr($id);
        $groups = Db::name('PageGroup')->where('id', 'in', $id)->select();
        if (!empty($groups)) {
            $groups = array_column($groups, 'group');
            $exists = Db::name('page')->where('group', 'in', $groups)->count();
            if ($exists > 0) {
                $this->error("选中的页面组还有内容");
            }
            $result = Db::name('PageGroup')->where('id', 'in', $id)->delete();
        }
        if ($result) {
            cache('page_group', NULL);
            $this->success(lang('Delete success!'), url('page/groups'));
        } else {
            $this->error(lang('Delete failed!'));
        }
    }
}
