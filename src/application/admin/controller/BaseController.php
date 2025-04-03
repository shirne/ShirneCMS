<?php

namespace app\admin\controller;

use app\admin\model\ManagerLoginModel;
use app\common\service\EncryptService;
use extcore\traits\Upload;
use think\Controller;
use think\Db;
use think\Exception;

/**
 * 后台基类
 * 自带基于方法名的权限验证
 * Class BaseController
 * @package app\admin\controller
 */
class BaseController extends Controller
{

    use Upload;

    protected $errMsg;
    protected $table;
    protected $model;

    protected $mid;
    protected $manager;
    protected $permision;

    protected $viewData = [];

    /**
     * 后台控制器全局初始化
     * @param $needLogin
     * @throws Exception
     */
    public function initialize()
    {
        parent::initialize();

        if (!defined('SUPER_ADMIN_ID')) define('SUPER_ADMIN_ID', config('super_admin_id'));
        if (!defined('TEST_ACCOUNT')) define('TEST_ACCOUNT', config('test_account'));

        $this->mid = session(SESSKEY_ADMIN_ID);

        $controller = strtolower($this->request->controller());
        if ($controller === 'login') {
            return;
        }
        //未登录的自动登录
        if (empty($this->mid)) {
            $this->autoLogin();
        }
        //判断用户是否登陆
        if (empty($this->mid)) {
            $this->error(lang('Please login first!'), url('admin/login/index'));
        }
        if (empty($this->manager)) {
            $this->manager = Db::name('Manager')->where('id', $this->mid)->find();
        }
        if (empty($this->manager)) {
            clearLogin();
            $this->error(lang('Invalid account!'), url('admin/login/index'));
        }
        if (TEST_ACCOUNT != $this->manager['username'] && $this->manager['login_time'] != session(SESSKEY_ADMIN_LAST_TIME)) {
            clearLogin();
            $this->error(lang('The account has login in other places!'), url('admin/login/index'));
        }

        //$controller=strtolower($this->request->controller());
        if ($controller != 'index') {
            $action = strtolower($this->request->action());
            if ($action != 'search') {
                if ($this->request->isPost() || $action == 'add' || $action == 'update') {
                    $this->checkPermision("edit");
                }
                if (strpos('del', $action) !== false || strpos('clear', $action) !== false) {
                    $this->checkPermision("del");
                }

                $this->checkPermision($controller . '_' . $action);
            }
        }

        if (!$this->request->isAjax()) {
            $this->assign('menus', getMenus());

            //空数据默认样式
            $this->assign('empty', list_empty());
        }
    }

    protected function autoLogin()
    {
        $loginsession = $this->request->cookie(SESSKEY_ADMIN_AUTO_LOGIN);
        if (!empty($loginsession)) {
            cookie(SESSKEY_ADMIN_AUTO_LOGIN, null);
            $data = EncryptService::getInstance()->decrypt($loginsession);
            if (!empty($data)) {
                $json = json_decode($data, true);
                if (!empty($json['hash'])) {
                    $login = ManagerLoginModel::where('hash', $json['hash'])->find();
                    if (!empty($login)) {
                        $timestamp = $json['time'];
                        if ($timestamp >= time()) {
                            $this->mid = $login['manager_id'];
                            $this->manager = Db::name('Manager')->where('id', $this->mid)->find();
                            setLogin($this->manager, 0);
                            $this->manager['login_time'] = session(SESSKEY_ADMIN_LAST_TIME);
                            $this->setAutoLogin($this->manager, $login['id']);
                        }
                    }
                }
            }
        }
    }

    protected function setAutoLogin($manager, $login_id = 0, $days = 7)
    {
        $expire = $days * 24 * 60 * 60;
        $timestamp = time() + $expire;
        $hash = ManagerLoginModel::createHash($manager['id']);
        $data = EncryptService::getInstance()->encrypt(json_encode(['hash' => $hash, 'time' => $timestamp]));
        cookie(SESSKEY_ADMIN_AUTO_LOGIN, $data, $expire);
        $data = [
            'hash' => $hash,
            'update_time' => time(),
            'login_time' => time(),
            'login_ip' => $this->request->ip(),
            'login_user_agent' => $this->request->server('user_agent'),
        ];
        if ($login_id > 0) {
            ManagerLoginModel::where('id', $login_id)->update($data);
        } else {
            $data['manager_id'] = $manager['id'];
            $data['create_time'] = $data['update_time'];
            $data['device'] = $this->parseDevice($data['login_user_agent']);
            $data['create_ip'] = $data['login_ip'];
            $data['create_user_agent'] = $data['login_user_agent'];
            ManagerLoginModel::create($data);
        }
    }

    private function parseDevice($userAgent)
    {

        return $this->request->isMobile() ? 'mobile' : 'pc';
    }

    public function _empty()
    {

        $this->error('页面不存在', url('index/index'));
    }

    /**
     * 检查权限
     * @param $permitem
     * @throws Exception
     */
    protected function checkPermision($permitem)
    {
        if (!$this->getPermision($permitem)) {
            $this->error(lang('You have no permission to do this operation!'));
        }
    }

    /**
     * 检查是否有权限
     * @param $permitem
     * @return bool
     * @throws Exception
     */
    protected function getPermision($permitem)
    {
        if ($this->manager['type'] == 1) {
            return true;
        }
        if (empty($this->permision)) {
            $this->permision = Db::name('ManagerPermision')->where('manager_id', $this->mid)->find();
            if (empty($this->permision)) {
                $this->error(lang('Bad permission settings, pls contact the manager!'));
            }
            $this->permision['global'] = explode(',', $this->permision['global']);
            $this->permision['detail'] = explode(',', $this->permision['detail']);
        }
        if (strpos($permitem, '_') > 0) {
            if (in_array($permitem, $this->permision['detail'])) return true;
        } else {
            if (in_array($permitem, $this->permision['global'])) return true;
        }
        return false;
    }

    protected function setAutoIncrement($table, $incre)
    {
        $incre = intval($incre);
        if ($incre < 1) {
            $this->error('起始id必须大于1');
        }
        $maxid = Db::name($table)->max('id');
        if ($incre < $maxid) {
            $this->error('起始id必须大于当前数据的最大id :' . $maxid);
        }

        try {
            $succed = Db::execute('ALTER TABLE ' . config('database.prefix') . $table . ' AUTO_INCREMENT = ' . intval($incre));
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
        if ($succed) {
            user_log($this->mid, 'set_increment', 1, '设置[' . $table . ']起始id' . $incre, 'manager');
            $this->success('设置成功');
        } else {
            $this->error('设置失败');
        }
    }

    /**
     * 兼容ajax的数据注册
     * @param mixed $name
     * @param string $value
     * @return $this
     */
    protected function assign($name, $value = '')
    {
        if ($this->request->isAjax()) {
            if (is_array($name)) {
                $this->viewData = array_merge($this->viewData, $name);
            } else {
                $this->viewData[$name] = $value;
            }
        } else {
            $this->view->assign($name, $value);
        }

        return $this;
    }

    /**
     * 兼容ajax的输出
     * @param string $template
     * @param array $vars
     * @param array $config
     * @return string
     * @throws \Throwable
     */
    protected function fetch($template = '', $vars = [], $config = [])
    {
        if ($this->request->isAjax()) {
            $this->result($this->viewData, 1);
        }

        return $this->view->fetch($template, $vars, $config);
    }
}
