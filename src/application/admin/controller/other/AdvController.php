<?php

namespace app\admin\controller\other;

use app\admin\controller\BaseController;
use app\admin\validate\AdvGroupValidate;
use app\admin\validate\AdvItemValidate;
use app\common\model\AdvGroupModel;
use app\common\model\AdvItemModel;
use think\Db;

/**
 * 广告功能
 * Class AdvController
 * @package app\admin\controller
 */
class AdvController extends BaseController
{
    public function search($key = '')
    {
        $model = Db::name('AdvGroup')
            ->where('status', 1);
        if (!empty($key)) {
            $model->where('id|title|flag', 'like', "%$key%");
        }

        $lists = $model->field('id,title,flag,status,create_time')
            ->order('id ASC')->limit(10)->select();
        return json(['data' => $lists, 'code' => 1]);
    }

    /**
     * 管理
     * @param $key
     * @return mixed
     * @throws \Throwable
     */
    public function index($key = '')
    {
        $model = Db::name('AdvGroup');
        if (!empty($key)) {
            $model->whereLike('title|flag', "%$key%");
        }
        $lists = $model->order('id DESC')->paginate(15);
        $this->assign('lists', $lists);
        $this->assign('page', $lists->render());
        return $this->fetch();
    }

    /**
     * 添加
     * @return mixed
     * @throws \Throwable
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $validate = new AdvGroupValidate();
            $validate->setId();
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                $created = AdvGroupModel::create($data);
                if ($created['id']) {
                    $this->success(lang('Add success!'), url('other.adv/index'));
                } else {
                    $this->error(lang('Add failed!'));
                }
            }
        }
        $model = array('status' => 1, 'type' => 0);
        $this->assign('model', $model);
        $this->assign('id', 0);
        return $this->fetch('update');
    }

    /**
     * 修改
     * @param $id
     * @return mixed
     * @throws \Throwable
     */
    public function update($id)
    {
        $id = intval($id);
        $model = AdvGroupModel::get($id);
        if (empty($model)) {
            $this->error('广告组不存在');
        }

        if ($this->request->isPost()) {
            $data = $this->request->post();
            $validate = new AdvGroupValidate();
            $validate->setId($id);

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                if (!isset($data['ext_set'])) $data['ext_set'] = [];
                try {
                    $model->allowField(true)->save($data);
                } catch (\Exception $err) {
                    $this->error(lang('Update failed: %s', [$err->getMessage()]));
                }
                $this->success(lang('Update success!'), url('other.adv/index'));
            }
        }

        $this->assign('model', $model);
        $this->assign('id', $id);
        return $this->fetch();
    }

    public function lock($id)
    {
        $booth = AdvGroupModel::get(intval($id));
        if (empty($booth)) {
            $this->error('广告位不存在');
        }
        $booth->save(['locked' => 1]);
        $this->success('锁定成功');
    }

    public function unlock($id)
    {
        $booth = AdvGroupModel::get(intval($id));
        if (empty($booth)) {
            $this->error('广告位不存在');
        }
        $booth->save(['locked' => 0]);
        $this->success('解锁成功');
    }

    /**
     * 删除广告位
     */
    public function delete($id)
    {
        $id = intval($id);
        $force = $this->request->post('force/d', 0);
        $model = Db::name('AdvGroup');
        $count = Db::name('AdvItem')->where('group_id', $id)->count();
        if ($count < 1 || $force != 0) {
            $result = $model->delete($id);
        } else {
            $result = false;
            $this->error("广告位中还有广告项目");
        }
        if ($result) {
            if ($count > 0) {
                Db::name('AdvItem')->where('group_id', $id)->delete();
            }
            $this->success(lang('Delete success!'), url('other.adv/index'));
        } else {
            $this->error(lang('Delete failed!'));
        }
    }

    /**
     * 广告列表
     * @param $gid
     * @param string $key
     * @return string
     * @throws \Throwable
     */
    public function itemlist($gid, $key = '')
    {
        $model = Db::name('AdvItem');
        $group = Db::name('AdvGroup')->find($gid);
        if (empty($group)) {
            $this->error('广告位不存在');
        }
        $model->where('group_id', $gid);
        if (!empty($key)) {
            $model->whereLike('title|url', "%$key%");
        }
        $lists = $model->order('sort ASC,id DESC')->paginate(15);
        $this->assign('lists', $lists);
        $this->assign('page', $lists->render());
        $this->assign('gid', $gid);
        return $this->fetch();
    }

    /**
     * 添加
     * @param $gid
     * @return mixed
     * @throws \Throwable
     */
    public function itemadd($gid)
    {
        $gid = intval($gid);
        $group = AdvGroupModel::get($gid);
        if (empty($group)) {
            $this->error('广告组不存在');
        }

        if ($this->request->isPost()) {
            $data = $this->request->post();
            $validate = new AdvItemValidate();

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                $uploaded = $this->_upload('banner', 'upload_image');
                if (!empty($uploaded)) {
                    $data['image'] = $uploaded['url'];
                } elseif ($this->uploadErrorCode > 102) {
                    $this->error($this->uploadErrorCode . ':' . $this->uploadError);
                }
                $uploaded = $this->_uploadFile('banner', 'upload_video', 2);
                if (!empty($uploaded)) {
                    $data['video'] = $uploaded['url'];
                } elseif ($this->uploadErrorCode > 102) {
                    $this->error($this->uploadErrorCode . ':' . $this->uploadError);
                }

                $url = url('other.adv/itemlist', array('gid' => $gid));
                $data['start_date'] = empty($data['start_date']) ? 0 : strtotime($data['start_date']);
                $data['end_date'] = empty($data['end_date']) ? 0 : strtotime($data['end_date']);
                if (isset($data['ext'])) {
                    $data['ext_data'] = $data['ext'];
                    unset($data['ext']);
                }
                if (isset($data['elements'])) {
                    $data['elements'] = $this->filterElements($data['elements']);
                }
                $model = AdvItemModel::create($data);
                if ($model['id']) {
                    $this->success(lang('Add success!'), $url);
                } else {
                    delete_image($data['image']);
                    $this->error(lang('Add failed!'));
                }
            }
        }
        $model = array('status' => 1, 'group_id' => $gid, 'ext' => []);
        $this->assign('group', $group);
        $this->assign('model', $model);
        $this->assign('id', 0);
        return $this->fetch('itemupdate');
    }

    /**
     * 修改
     */
    public function itemupdate($id)
    {
        $id = intval($id);
        $model = Db::name('AdvItem')->where('id', $id)->find();
        if (empty($model)) {
            $this->error('广告项不存在');
        }
        $model = AdvGroupModel::fixAdItem($model);
        $group = AdvGroupModel::get($model['group_id']);
        if (empty($group)) {
            $this->error('广告组不存在');
        }

        if ($this->request->isPost()) {
            $data = $this->request->post();
            $validate = new AdvItemValidate();

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                $model = AdvItemModel::where('id', $id)->find();
                $url = url('other.adv/itemlist', array('gid' => $data['group_id']));
                $delete_images = [];
                $uploaded = $this->_upload('banner', 'upload_image');
                if (!empty($uploaded)) {
                    $data['image'] = $uploaded['url'];
                    if (!empty($data['delete_image'])) $delete_images[] = $data['delete_image'];
                } elseif ($this->uploadErrorCode > 102) {
                    $this->error($this->uploadErrorCode . ':' . $this->uploadError);
                }
                if (isset($data['delete_image'])) unset($data['delete_image']);

                $uploaded = $this->_uploadFile('banner', 'upload_video', 2);
                if (!empty($uploaded)) {
                    $data['video'] = $uploaded['url'];
                } elseif ($this->uploadErrorCode > 102) {
                    $this->error($this->uploadErrorCode . ':' . $this->uploadError);
                }

                $data['start_date'] = empty($data['start_date']) ? 0 : strtotime($data['start_date']);
                $data['end_date'] = empty($data['end_date']) ? 0 : strtotime($data['end_date']);
                if (isset($data['ext'])) {
                    $data['ext_data'] = $data['ext'];
                    unset($data['ext']);
                }
                if (isset($data['elements'])) {
                    $data['elements'] = $this->filterElements($data['elements']);
                }

                if ($model->allowField(true)->save($data)) {
                    delete_image($delete_images);
                    $this->success(lang('Update success!'), $url);
                } else {
                    delete_image($data['image']);
                    $this->error(lang('Update failed!'));
                }
            }
        }

        $this->assign('group', $group);
        $this->assign('model', $model);
        $this->assign('id', $id);
        return $this->fetch();
    }

    private function filterElements($elements)
    {
        $fields = [];
        foreach ($elements as $k => $item) {
            if ($item['type'] == 'image') {
                $fields[] = "elements_{$k}_image";
            }
        }

        $uploaded = $this->_batchUpload('banner', $fields);
        if (!empty($uploaded)) {
            foreach ($uploaded as $k => $file) {
                $newkey = explode('_', $k . '_');
                $newkey = $newkey[1];
                $elements[$newkey]['image'] = $file;
            }
        } elseif ($this->uploadErrorCode > 102) {
            $this->error($this->uploadErrorCode . ':' . $this->uploadError);
        }
        return array_values($elements);
    }

    /**
     * 删除广告
     */
    public function itemdelete($gid, $id)
    {
        $id = intval($id);
        $model = Db::name('AdvItem');
        $result = $model->delete($id);
        if ($result) {
            $this->success(lang('Delete success!'), url('other.adv/itemlist', array('gid' => $gid)));
        } else {
            $this->error(lang('Delete failed!'));
        }
    }
}

//end