<?php

namespace app\admin\controller;

use app\admin\validate\OauthValidate;
use think\Db;


/**
 * 第三方登录配置
 * Class OauthController
 * @package app\admin\controller
 */
class OauthController extends BaseController
{
    /**
     * 列表
     */
    public function index($key = "")
    {
        $model = Db::name('OAuth');
        $lists = $model->order('ID ASC')->paginate(15);
        $this->assign('lists', $lists);
        $this->assign('page', $lists->render());
        $this->assign('types', getOauthTypes());
        return $this->fetch();
    }

    /**
     * 添加
     * @return mixed
     */
    public function add()
    {
        if ($this->request->isPost()) {
            //如果用户提交数据
            $data = $this->request->post();
            $validate = new OauthValidate();

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {

                $uploaded = $this->_upload('oauth', 'upload_logo', false);
                if (!empty($uploaded)) {
                    $data['logo'] = $uploaded['url'];
                } elseif ($this->uploadErrorCode > 102) {
                    $this->error($this->uploadErrorCode . ':' . $this->uploadError);
                }

                if (Db::name('OAuth')->insert($data)) {
                    $this->success(lang('Add success!'), url('oauth/index'));
                } else {
                    $this->error(lang('Add failed!'));
                }
            }
        }
        $model = array();
        $this->assign('types', getOauthTypes());
        $this->assign('model', $model);
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
        if ($this->request->isPost()) {
            //如果用户提交数据
            $data = $this->request->post();
            $validate = new OauthValidate();

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                $data['id'] = $id;
                $delete_images = [];
                $uploaded = $this->_upload('oauth', 'upload_logo', false);
                if (!empty($uploaded)) {
                    $data['logo'] = $uploaded['url'];
                    if (!empty($data['delete_logo'])) $delete_images[] = $data['delete_logo'];
                } elseif ($this->uploadErrorCode > 102) {
                    $this->error($this->uploadErrorCode . ':' . $this->uploadError);
                }
                if (isset($data['delete_logo'])) unset($data['delete_logo']);

                if (Db::name('OAuth')->update($data)) {
                    delete_image($delete_images);
                    $this->success(lang('Update success!'), url('oauth/index'));
                } else {
                    $this->error(lang('Update failed!'));
                }
            }
        }

        $model = Db::name('OAuth')->find($id);
        if (empty($model)) {
            $this->error('数据不存在');
        }
        $this->assign('types', getOauthTypes());
        $this->assign('model', $model);
        $this->assign('id', $id);
        return $this->fetch();
    }

    /**
     * 删除
     * @param $id
     */
    public function delete($id)
    {
        $id = intval($id);
        $model = Db::name('OAuth');
        $result = $model->delete($id);
        if ($result) {
            $this->success(lang('Delete success!'), url('oauth/index'));
        } else {
            $this->error(lang('Delete failed!'));
        }
    }
}
