<?php

namespace app\admin\controller\member;

use app\admin\controller\BaseController;
use app\common\model\MemberAgentModel;
use app\common\model\MemberLevelModel;
use app\common\model\MemberModel;
use app\common\validate\MemberValidate;
use shirne\excel\Excel;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use think\Db;

/**
 * 会员管理
 * Class MemberController
 * @package app\admin\controller
 */
class IndexController extends BaseController
{
    public function initialize()
    {
        parent::initialize();

        Db::name('Manager')->where('id', $this->manager['id'])->update(array('last_view_member' => time()));
    }

    /**
     * 会员搜索接口
     * @param $key
     * @param int $type
     * @return \think\response\Json
     */
    public function search($key = '', $type = 0, $is_agent = -1)
    {
        $model = Db::name('member')
            ->where('status', 1);
        if (!empty($key)) {
            $model->where('id|username|realname|mobile|agentcode', 'like', "%$key%");
        }
        if (!empty($type)) {
            $model->where('type', $type);
        }
        if ($is_agent > 0) {
            $model->where('is_agent', '>', 0);
        } elseif ($is_agent > -1) {
            $model->where('is_agent', 0);
        }

        $lists = $model->field('id,username,nickname,realname,mobile,avatar,level_id,is_agent,agentcode,gender,email,create_time')
            ->order('id ASC')->limit(10)->select();
        if (!empty($lists)) {
            $levels = MemberLevelModel::getCacheData();
            $agents = MemberAgentModel::getCacheData();
            foreach ($lists as &$item) {
                if (isset($levels[$item['level_id']])) {
                    $item['level'] = $levels[$item['level_id']];
                } else {
                    $item['level'] = new \stdClass();
                }
                if (isset($agents[$item['is_agent']])) {
                    $item['agent'] = $agents[$item['is_agent']];
                } else {
                    $item['agent'] = new \stdClass();
                }
            }
            unset($item);
        }
        return json(['data' => $lists, 'code' => 1]);
    }

    /**
     * 会员列表
     * @param int $type
     * @param string $start_date
     * @param string $end_date
     * @param string $keyword
     * @param string $referer
     * @return mixed|\think\response\Redirect
     */
    public function index($type = 0, $status = '', $start_date = '', $end_date = '', $keyword = '', $referer = '', $page_size = 15)
    {
        if ($this->request->isPost()) {
            return redirect(url('', ['referer' => $referer, 'start_date' => $start_date, 'end_date' => $end_date, 'type' => $type, 'page_size' => $page_size, 'keyword' => base64url_encode($keyword)]));
        }
        $keyword = empty($keyword) ? "" : base64url_decode($keyword);
        $model = Db::view('__MEMBER__ m', '*')
            ->view('__MEMBER__ rm', ['username' => 'refer_name', 'nickname' => 'refer_nickname', 'realname' => 'refer_realname', 'avatar' => 'refer_avatar', 'is_agent' => 'refer_agent'], 'm.referer=rm.id', 'LEFT');
        if (!empty($keyword)) {
            $model->whereLike('m.username|m.nickname|m.mobile|m.email|m.realname|m.agentcode', "%$keyword%");
        }

        $this->assign('refid', intval($referer));
        if ($referer !== '') {
            if ($referer != '0') {
                $member = Db::name('Member')->where('id|username', $referer)->find();
                if (empty($member)) {
                    $this->error('填写的会员不存在');
                }
                $model->where('m.referer', $member['id']);
                $this->assign('refid', $member['id']);
            } else {
                $model->where('m.referer', intval($referer));
            }
        }
        if ($status !== '') {
            $model->where('m.status', intval($status));
        }
        if ($type > 0) {
            $model->where('m.type', intval($type) - 1);
        }

        if ($start_date !== '') {
            if ($end_date !== '') {
                $model->whereBetween('m.create_time', [strtotime($start_date), strtotime($end_date . ' 23:59:59')]);
            } else {
                $model->where('m.create_time', 'GT', strtotime($start_date));
            }
        } else {
            if ($end_date !== '') {
                $model->where('m.create_time', 'LT', strtotime($end_date . ' 23:59:59'));
            }
        }

        $lists = $model->order('m.id desc')->paginate($page_size);

        $this->assign('lists', $lists);
        $this->assign('page', $lists->render());
        $this->assign('memberids', implode(',', array_column($lists->items(), 'id')));
        $this->assign('moneyTypes', getMoneyFields(false));
        $this->assign('types', getMemberTypes());
        $this->assign('typestyles', ['default', 'info', 'warning', 'danger']);
        $this->assign('levels', getMemberLevels());
        $this->assign('agents', MemberAgentModel::getCacheData());
        $this->assign('type', $type);
        $this->assign('status', intval($status));
        $this->assign('referer', $referer);
        $this->assign('start_date', $start_date);
        $this->assign('end_date', $end_date);
        $this->assign('keyword', $keyword);
        $this->assign('page_size', $page_size);
        return $this->fetch();
    }

    /**
     * 导出会员
     * @param string $ids
     * @param string $type
     * @param string $keyword
     * @param string $refid
     * @param string $start_date
     * @param string $end_date
     * @param string $status
     * @param string $subscrib
     * @param string $fields
     */
    public function export($ids = '', $type = '', $keyword = '', $refid = '', $start_date = '', $end_date = '', $status = '', $subscrib = '', $fields = 'mobile')
    {
        $keyword = empty($keyword) ? "" : base64_decode($keyword);
        $model = Db::view('__MEMBER__ m', '*')
            ->view('__MEMBER__ rm', ['username' => 'refer_name', 'nickname' => 'refer_nickname', 'realname' => 'refer_realname', 'avatar' => 'refer_avatar', 'is_agent' => 'refer_agent'], 'm.referer=rm.id', 'LEFT');
        if (empty($ids)) {
            if (!empty($keyword)) {
                $model->whereLike('m.username|m.nickname|m.email|m.realname', "%$keyword%");
            }
            if ($type !== '') {
                $model->where('m.type', intval($type) - 1);
            }
            if ($status !== '') {
                $model->where('m.status', intval($status));
            }
            if ($refid !== '') {
                $model->where('m.referer', intval($refid));
            }
            if ($subscrib !== '') {
                $model->where('m.is_subscrib', $subscrib);
            }
            if ($start_date !== '') {
                if ($end_date !== '') {
                    $model->whereBetween('m.create_time', [strtotime($start_date), strtotime($end_date . ' 23:59:59')]);
                } else {
                    $model->where('m.create_time', 'GT', strtotime($start_date));
                }
            } else {
                if ($end_date !== '') {
                    $model->where('m.create_time', 'LT', strtotime($end_date . ' 23:59:59'));
                }
            }
        } elseif ($ids == 'status') {
            $model->where('m.status', 1);
        } else {
            $model->whereIn('m.id', idArr($ids));
        }

        $fields = explode(',', $fields);
        $rows = $model->order('m.create_time DESC')->select();
        if (empty($rows)) {
            $this->error('没有选择要导出的项目');
        }

        $excel = new Excel();
        $headers = [];
        if (in_array('id', $fields)) {
            $headers[] = 'ID';
        }
        if (in_array('username', $fields)) {
            $headers[] = '用户名';
        }
        if (in_array('nickname', $fields)) {
            $headers[] = '昵称';
        }
        if (in_array('realname', $fields)) {
            $headers[] = '姓名';
        }
        if (in_array('company', $fields)) {
            $headers[] = '公司';
        }
        if (in_array('mobile', $fields)) {
            $headers[] = '手机号';
        }
        if (in_array('email', $fields)) {
            $headers[] = '邮箱';
        }
        if (in_array('balance', $fields) || in_array('money', $fields)) {
            $headers[] = '余额';
        }
        if (in_array('credit', $fields)) {
            $headers[] = '积分';
        }
        if (in_array('reward', $fields)) {
            $headers[] = '佣金';
        }
        $excel->setHeader($headers);
        for ($i = 0; $i < count($headers); $i++) {
            $excel->setColumnType(chr(ord('A') + $i), DataType::TYPE_STRING);
        }

        foreach ($rows as $row) {
            $rValues = [];
            if (in_array('id', $fields)) {
                $rValues[] = $row['id'];
            }
            if (in_array('username', $fields)) {
                $rValues[] = $row['username'];
            }
            if (in_array('nickname', $fields)) {
                $rValues[] = $row['nickname'];
            }
            if (in_array('realname', $fields)) {
                $rValues[] = $row['realname'];
            }
            if (in_array('company', $fields)) {
                $headers[] = $row['company'];
            }
            if (in_array('mobile', $fields)) {
                $rValues[] = $row['mobile'];
            }
            if (in_array('email', $fields)) {
                $rValues[] = $row['email'];
            }
            if (in_array('balance', $fields) || in_array('money', $fields)) {
                $rValues[] = $row['money'];
            }
            if (in_array('credit', $fields)) {
                $rValues[] = $row['credit'];
            }
            if (in_array('reward', $fields)) {
                $rValues[] = $row['reward'];
            }
            $excel->addRow($rValues);
        }

        $excel->output(date('Y-m-d-H-i') . '-会员导出[' . count($rows) . '条]');
    }

    public function set_increment($incre)
    {
        $this->setAutoIncrement('member', $incre);
    }

    public function set_level($id = 0, $level_id = 0)
    {
        if (empty($id) || empty($level_id)) $this->error('参数错误');
        $id = intval($id);
        $level_id = intval($level_id);

        $member = MemberModel::get($id);
        if (empty($member)) $this->error('会员不存在');

        $result = $member->save(['level_id' => $level_id]);
        if ($result) {
            user_log($this->mid, 'setlevel', 1, '设置会员等级 ' . $id . '/' . $level_id, 'manager');
            $this->success('设置成功');
        } else {
            $this->error('设置失败');
        }
    }

    public function set_referer($id = 0, $referer = 0)
    {
        if (empty($id) || empty($referer)) $this->error('参数错误');
        $id = intval($id);
        $referer = intval($referer);

        $member = MemberModel::get($id);
        if (empty($member)) $this->error('会员不存在');

        $result = $member->setReferer($referer);
        if ($result) {
            user_log($this->mid, 'setreferer', 1, '设置推荐人 ' . $id . '/' . $referer, 'manager');
            $this->success('设置成功');
        } else {
            $this->error('设置失败：' . $member->getError());
        }
    }

    public function del_referer($id = 0)
    {
        if (empty($id)) $this->error('参数错误');
        $id = intval($id);

        $member = MemberModel::get($id);
        if (empty($member)) $this->error('会员不存在');

        $result = $member->clrReferer();
        if ($result) {
            user_log($this->mid, 'delreferer', 1, '清除推荐人 ' . $id . '/' . $member['referer'], 'manager');
            $this->success('清除成功');
        } else {
            $this->error('清除失败');
        }
    }

    /**
     * 设置代理
     * @param int $id
     */
    public function set_agent($id = 0, $agent_id = 1)
    {
        if (empty($id)) $this->error('会员不存在');
        $member = Db::name('member')->find($id);
        if (empty($member)) $this->error('会员不存在');

        $province = $this->request->param('province');
        $city = $this->request->param('city');

        if ($agent_id == 3) {
            if (empty($province) || empty($city)) {
                $this->error('请指定省市');
            }
        }
        if ($agent_id == 4) {
            if (empty($province)) {
                $this->error('请指定省份');
            }
        }

        if ($agent_id < 1) {
            $this->error('参数错误');
        }
        if ($agent_id > 1) {
            Db::name('member')->where('id', $id)->update([
                'agent_province' => $province,
                'agent_city' => $city,
            ]);
        }
        if ($member['is_agent'] == $agent_id) $this->success('设置成功');

        $result = MemberModel::setAgent($id, $agent_id, 'admin', '后台升级');
        if ($result) {
            if ($member['is_agent'] < 1) {
                MemberModel::updateRecommend($member['referer']);
            }
            user_log($this->mid, 'setagent', 1, '设置代理 ' . $id, 'manager');
            $this->success('设置成功');
        } else {
            $this->error('设置失败');
        }
    }

    /**
     * 取消代理
     * @param int $id
     */
    public function cancel_agent($id = 0)
    {
        if (empty($id)) $this->error('会员不存在');
        $member = Db::name('member')->find($id);
        if (empty($member)) $this->error('会员不存在');
        if ($member['is_agent'] == 0) $this->success('取消成功');

        $result = MemberModel::cancelAgent($id);
        if ($result) {
            user_log($this->mid, 'cancelagent', 1, '取消代理 ' . $id, 'manager');
            $this->success('取消成功');
            exit;
        } else {
            $this->error('取消失败');
        }
    }

    /**
     * 佣金记录
     * @param int $id
     * @param int $from_id
     * @param string $fromdate
     * @param string $todate
     * @param string $status
     * @param string $type
     * @return mixed
     */
    public function award_log($id = 0, $from_id = 0, $fromdate = '', $todate = '', $status = '', $type = 'all', $orderid = 0)
    {
        $model = Db::view('AwardLog mlog', '*')
            ->view('Member m', ['username', 'nickname', 'avatar', 'level_id', 'mobile'], 'm.id=mlog.member_id', 'LEFT')
            ->view('Member fm', ['username' => 'from_username', 'nickname' => 'from_nickname', 'avatar' => 'from_avatar', 'level_id' => 'from_level_id', 'mobile' => 'from_mobile'], 'fm.id=mlog.from_member_id', 'LEFT');

        $levels = getMemberLevels();

        if ($id > 0) {
            $model->where('mlog.member_id', $id);
            $this->assign('member', Db::name('member')->find($id));
        }
        if ($from_id > 0) {
            $model->where('mlog.from_member_id', $from_id);
            $this->assign('from_member', Db::name('member')->find($from_id));
        }
        if ($orderid > 0) {
            $orderid = intval($orderid);
            $model->where('mlog.order_id', $orderid);
            $this->assign('orderid', $orderid);
            $this->assign('order', Db::name('order')->find($orderid));
        }
        if (!empty($type) && $type != 'all') {
            $model->where('mlog.type', $type);
        } else {
            $type = 'all';
        }
        if ($status !== '') {
            $model->where('mlog.status', $status);
        }

        if (!empty($todate)) {
            $totime = strtotime($todate . ' 23:59:59');
            if ($totime === false) $todate = '';
        }
        if (!empty($fromdate)) {
            $fromtime = strtotime($fromdate);
            if ($fromtime === false) $fromdate = '';
        }
        if (!empty($fromtime)) {
            if (!empty($totime)) {
                $model->whereBetween('mlog.create_time', array($fromtime, $totime));
            } else {
                $model->where('mlog.create_time', 'EGT', $fromtime);
            }
        } else {
            if (!empty($totime)) {
                $model->where('mlog.create_time', 'ELT', $totime);
            }
        }

        $logs = $model->order('ID DESC')->paginate(15);

        $types = getLogTypes();
        $allstatus = ['-1' => '已取消', '0' => '待发放', '1' => '已发放'];

        $stacrows = $model->group('mlog.status,mlog.type')->setOption('field', [])->setOption('order', 'mlog.status')->field('mlog.status,mlog.type,sum(mlog.amount) as total_amount')->select();
        $statics = [];
        foreach ($stacrows as $row) {
            $statics[$row['status']][$row['type']] = $row['total_amount'];
        }
        foreach ($statics as $k => $list) {
            $statics[$k]['sum'] = array_sum($statics[$k]);
        }

        $this->assign('id', $id);
        $this->assign('from_id', $from_id);
        $this->assign('fromdate', $fromdate);
        $this->assign('todate', $todate);
        $this->assign('type', $type);
        $this->assign('status', $status);

        $this->assign('types', $types);
        $this->assign('allstatus', $allstatus);
        $this->assign('levels', $levels);
        $this->assign('statics', $statics);
        $this->assign('logs', $logs);
        $this->assign('page', $logs->render());
        return $this->fetch();
    }

    /**
     * 余额记录
     * @param int $id
     * @param int $from_id
     * @param string $fromdate
     * @param string $todate
     * @param string $field
     * @param string $type
     * @return mixed
     */
    public function money_log($id = 0, $from_id = 0, $fromdate = '', $todate = '', $field = 'all', $type = 'all')
    {
        $model = Db::view('MemberMoneyLog mlog', '*')
            ->view('Member m', ['username', 'nickname', 'avatar', 'level_id', 'mobile'], 'm.id=mlog.member_id', 'LEFT')
            ->view('Member fm', ['username' => 'from_username', 'nickname' => 'from_nickname', 'avatar' => 'from_avatar', 'level_id' => 'from_level_id', 'mobile' => 'from_mobile'], 'fm.id=mlog.from_member_id', 'LEFT');

        $levels = getMemberLevels();

        if ($id > 0) {
            $model->where('mlog.member_id', $id);
            $this->assign('member', Db::name('member')->find($id));
        }
        if ($from_id > 0) {
            $model->where('mlog.from_member_id', $from_id);
            $this->assign('from_member', Db::name('member')->find($from_id));
        }
        if (!empty($type) && $type != 'all') {
            $model->where('mlog.type', $type);
        } else {
            $type = 'all';
        }
        if (!empty($field) && $field != 'all') {
            $model->where('mlog.field', $field);
        } else {
            $field = 'all';
        }

        if (!empty($todate)) {
            $totime = strtotime($todate . ' 23:59:59');
            if ($totime === false) $todate = '';
        }
        if (!empty($fromdate)) {
            $fromtime = strtotime($fromdate);
            if ($fromtime === false) $fromdate = '';
        }
        if (!empty($fromtime)) {
            if (!empty($totime)) {
                $model->whereBetween('mlog.create_time', array($fromtime, $totime));
            } else {
                $model->where('mlog.create_time', 'EGT', $fromtime);
            }
        } else {
            if (!empty($totime)) {
                $model->where('mlog.create_time', 'ELT', $totime);
            }
        }

        $logs = $model->order('ID DESC')->paginate(15);

        $types = getLogTypes();
        $fields = getMoneyFields();
        $stacrows = $model->group('mlog.field,mlog.type')->setOption('field', [])->setOption('order', 'mlog.field')->field('mlog.field,mlog.type,sum(mlog.amount) as total_amount')->select();
        $statics = [];
        foreach ($stacrows as $row) {
            $statics[$row['field']][$row['type']] = $row['total_amount'];
        }
        foreach ($statics as $k => $list) {
            $statics[$k]['sum'] = array_sum($statics[$k]);
        }

        $this->assign('id', $id);
        $this->assign('from_id', $from_id);
        $this->assign('fromdate', $fromdate);
        $this->assign('todate', $todate);
        $this->assign('type', $type);
        $this->assign('field', $field);

        $this->assign('types', $types);
        $this->assign('fields', $fields);
        $this->assign('levels', $levels);
        $this->assign('statics', $statics);
        $this->assign('logs', $logs);
        $this->assign('page', $logs->render());
        return $this->fetch();
    }

    /**
     * 会员日志
     * @param string $key
     * @param string $type
     * @param int $member_id
     * @return mixed
     */
    public function log($key = '', $type = '', $member_id = 0)
    {
        if ($this->request->isPost()) {
            return redirect(url('', ['key' => base64url_encode($key)]));
        }

        $model = Db::view('MemberLog', '*')
            ->view('Member', ['username', 'nickname', 'avatar'], 'MemberLog.member_id=Member.id', 'LEFT');

        if (!empty($key)) {
            $key = base64url_decode($key);
            $model->whereLike('ManagerLog.remark', "%$key%");
        }
        if (!empty($type)) {
            $model->where('action', $type);
        }
        if ($member_id != 0) {
            $model->where('member_id', $member_id);
        }

        $logs = $model->order('MemberLog.id DESC')->paginate(15);
        $this->assign('lists', $logs);
        $this->assign('keyword', $key);
        $this->assign('page', $logs->render());
        return $this->fetch();
    }

    /**
     * 日志详情
     * @param $id
     * @return mixed
     */
    public function logview($id)
    {
        $model = Db::name('MemberLog');

        $m = $model->find($id);
        $member = Db::name('Member')->find($m['member_id']);

        $this->assign('m', $m);
        $this->assign('member', $member);
        return $this->fetch();
    }

    /**
     * 清除日志
     */
    public function logclear()
    {
        $date = $this->request->get('date');
        $d = strtotime($date);
        if (empty($d)) {
            $d = strtotime('-7days');
        }

        $model = Db::name('MemberLog');

        $model->where('create_time', 'ELT', $d)->delete();

        user_log($this->mid, 'clearmemberlog', 1, '清除会员日志', 'manager');
        $this->success("清除完成");
    }

    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            //如果用户提交数据
            $data = $this->request->post();
            $validate = new MemberValidate();
            $validate->setId();
            if (!$validate->scene('register')->check($data)) {
                $this->error($validate->getError());
            } else {
                $data['salt'] = random_str(8);
                $data['password'] = encode_password($data['password'], $data['salt']);
                if (!isset($data['level_id'])) {
                    $data['level_id'] = getDefaultLevel();
                }
                if (isset($data['birth'])) {
                    $data['birth'] = strtotime($data['birth']);
                }
                $member = MemberModel::create($data);
                if ($member->id) {
                    user_log($this->mid, 'adduser', 1, '添加会员' . $member->id, 'manager');
                    $this->success(lang('Add success!'), url('member.index/index'));
                } else {
                    $this->error(lang('Add failed!'));
                }
            }
        }
        $model = array('type' => 1, 'status' => 1, 'gender' => 0);
        $this->assign('model', $model);
        $this->assign('types', getMemberTypes());
        return $this->fetch('update');
    }

    /**
     * 修改
     * @param $id
     * @return mixed
     */
    public function update($id)
    {
        $id = intval($id);
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $validate = new MemberValidate();
            $validate->setId($id);
            if (!$validate->scene('edit')->check($data)) {
                $this->error($validate->getError());
            } else {
                if (!empty($data['password'])) {
                    $data['salt'] = random_str(8);
                    $data['password'] = encode_password($data['password'], $data['salt']);
                } else {
                    unset($data['password']);
                }
                if (isset($data['birth'])) {
                    $data['birth'] = strtotime($data['birth']);
                }

                // 更新
                $member = MemberModel::get($id);
                if (empty($member)) {
                    $this->error('会员资料错误');
                }
                if (isset($data['mobile'])) {
                    $data['mobile_bind'] = intval($data['mobile_bind']);
                    if (empty($data['mobile'])) {
                        $data['mobile_bind'] = 0;
                    }
                }
                if (isset($data['email'])) {
                    $data['email_bind'] = intval($data['email_bind']);
                    if (empty($data['email'])) {
                        $data['email_bind'] = 0;
                    }
                }

                if ($member->allowField(true)->save($data)) {
                    user_log($this->mid, 'updateuser', 1, '修改会员资料' . $id, 'manager');
                    $this->success(lang('Update success!'), url('member.index/index'));
                } else {
                    $this->error(lang('Update failed!'));
                }
            }
        }
        $model = Db::name('Member')->find($id);
        $this->assign('types', getMemberTypes());
        $this->assign('model', $model);
        return $this->fetch();
    }

    /**
     * 充值
     */
    public function recharge()
    {
        $id = $this->request->post('id/d');
        $field = $this->request->post('field');
        $amount = $this->request->post('amount');
        $reson = $this->request->post('reson');
        if (floatval($amount) != $amount) {
            $this->error('金额错误');
        }
        if (!in_array($field, ['money', 'credit', 'reward'])) {
            $this->error('充值类型错误');
        }

        $atext = $amount > 0 ? '充值' : '扣款';
        $logid = money_log($id, intval($amount * 100), '系统' . $atext . $reson, 'system', 0, $field);
        if ($logid) {
            user_log($this->mid, 'recharge', 1, '会员' . $atext . ' ' . $id . ',' . $amount, 'manager');
            $this->success($atext . '成功');
        } else {
            $this->error($atext . '失败');
        }
    }

    /**
     * 会员状态
     */
    public function status($id, $type = 0)
    {
        $model = Db::name('member');
        $data['status'] = $type == 1 ? 1 : 0;
        if ($model->where('id', 'in', idArr($id))->update($data)) {
            user_log($this->mid, $type == 1 ? 'enableuser' : 'disableuser', 1, ($type == 1 ? '启用会员' : '禁用会员') . ':' . $id, 'manager');
            $this->success(lang('Update success!'), url('member.index/index'));
        } else {
            $this->error(lang('Update failed!'));
        }
    }

    public function delete($id)
    {
        $ids = idArr($id);
        if (empty($id) || empty($ids)) {
            $this->error('参数错误');
        }
        $deleted = Db::name('member')->whereIn('id', $ids)->delete();
        if ($deleted) {
            //删除相关表
            $tables = Db::query('show tables');
            $field = 'Tables_in_' . config('database.database');

            foreach ($tables as $row) {
                $columns = Db::query('show columns in ' . $row[$field]);
                $fields = array_column($columns, 'Field');
                if (in_array('member_id', $fields)) {
                    Db::table($row[$field])->whereIn('member_id', $id)->delete();
                }
            }

            user_log($this->mid, 'deleteuser', 1, '删除会员:' . $id, 'manager');
            $this->success(lang('Delete success!'), url('member.index/index'));
        } else {
            $this->error(lang('Delete failed!'));
        }
    }

    /**
     * 会员注册情况统计
     * @param string $type
     * @param string $start_date
     * @param string $end_date
     * @return mixed
     */
    public function statics($type = 'date', $start_date = '', $end_date = '')
    {
        if ($this->request->isPost()) {
            if (!in_array($type, ['date', 'month', 'year'])) $type = 'date';
            return redirect(url('', ['type' => $type, 'start_date' => $start_date, 'end_date' => $end_date]));
        }
        $format = "'%Y-%m-%d'";

        if ($type == 'month') {
            $format = "'%Y-%m'";
        } elseif ($type == 'year') {
            $format = "'%Y'";
        }

        $model = Db::name('member')->field('count(id) as member_count,date_format(from_unixtime(create_time),' . $format . ') as awdate');
        $logModel = Db::name('memberAgentLog')->where('agent_id', 1)->field('count(id) as agent_count,date_format(from_unixtime(create_time),' . $format . ') as awdate');

        $start_date = format_date($start_date, 'Y-m-d');
        $end_date = format_date($end_date, 'Y-m-d');
        if (!empty($start_date)) {
            if (!empty($end_date)) {
                $model->whereBetween('create_time', [strtotime($start_date), strtotime($end_date . ' 23:59:59')]);
                $logModel->whereBetween('create_time', [strtotime($start_date), strtotime($end_date . ' 23:59:59')]);
            } else {
                $model->where('create_time', 'GT', strtotime($start_date));
                $logModel->where('create_time', 'GT', strtotime($start_date));
            }
        } else {
            if (!empty($end_date)) {
                $model->where('create_time', 'LT', strtotime($end_date . ' 23:59:59'));
                $logModel->where('create_time', 'LT', strtotime($end_date . ' 23:59:59'));
            }
        }

        $statics = $model->group('awdate')->select();
        $logStatics = $logModel->group('awdate')->select();
        $dates = array_merge(array_column($statics, 'awdate'), array_column($logStatics, 'awdate'));
        $dates = array_unique($dates);

        $statics = array_column($statics, 'member_count', 'awdate');
        $logStatics = array_column($logStatics, 'agent_count', 'awdate');

        $newStatics = [];
        foreach ($dates as $date) {
            $newStatics[] = [
                'awdate' => $date,
                'member_count' => isset($statics[$date]) ? $statics[$date] : 0,
                'agent_count' => isset($logStatics[$date]) ? $logStatics[$date] : 0
            ];
        }

        $this->assign('statics', $newStatics);
        $this->assign('static_type', $type);
        $this->assign('start_date', $start_date);
        $this->assign('end_date', $end_date);
        return $this->fetch();
    }
}
