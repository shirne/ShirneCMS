<?php

namespace app\admin\controller\member;

use app\admin\controller\BaseController;
use think\Db;

/**
 * 邀请码管理
 * Class InviteController
 * @package app\admin\controller
 */
class InviteController extends BaseController
{
    /**
     * 邀请码列表
     * @param string $keyword
     * @param int $accurate
     * @return mixed
     */
    public function index($keyword = '', $accurate = 0)
    {
        if ($this->request->isPost()) {
            return redirect(url('', ['keyword' => base64url_encode($keyword), 'accurate' => $accurate]));
        }
        $keyword = empty($keyword) ? '' : trim(base64url_decode($keyword));
        $model = Db::view('inviteCode', '*')
            ->view('member', ['username', 'nickname', 'avatar', 'level_id'], 'member.id=inviteCode.member_id', 'LEFT')
            ->view('member memberUse', ['username' => 'use_username', 'nickname' => 'use_nickname', 'avatar' => 'use_avatar', 'level_id' => 'use_level_id'], 'memberUse.id=inviteCode.member_use', 'LEFT');

        if (!empty($keyword)) {
            if ($accurate == 1) {
                $model->whereLike('member.nickname|member.username|member.mobile', "%$keyword%");
            } elseif ($accurate == 2) {
                $model->whereLike('memberUse.nickname|memberUse.username|memberUse.mobile', "%$keyword%");
            } else {
                $accurate = 0;
                $model->where('inviteCode.code', $keyword);
            }
        }

        $lists = $model->paginate(15);
        $this->assign('lists', $lists);
        $this->assign('page', $lists->render());
        $this->assign('levels', getMemberLevels());
        $this->assign('keyword', $keyword);
        $this->assign('accurate', $accurate);
        return $this->fetch();
    }


    /**
     * 生成邀请码
     * @return mixed
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $model = Db::name("invite_code");
            $mem_id = $this->request->post('member_id/d');
            $level_id = $this->request->post('level_id/d');
            $length = $this->request->post('length/d');
            $number = $this->request->post('number/d');
            $date = $this->request->post('valid_date');

            if ($length < 8 || $length > 16) $this->error('激活码长度需在8-16位之间');
            if ($number > 1000) $this->error('每次生成数量在 1000以内');
            $member = Db::name('member')->where('id', $mem_id)->find();
            if (empty($member)) $this->error('指定的会员不存在');
            $invalid = 0;
            if (!empty($date)) {
                $d = strtotime($date);
                if ($d) $invalid = $d;
            }


            $data = array();
            $data['member_id'] = $mem_id;
            $data['level_id'] = $level_id;
            $data['invalid_time'] = $invalid;
            $data['is_lock'] = 0;
            $data['create_time'] = time();
            $data['member_use'] = 0;
            $data['use_time'] = 0;
            for ($i = 0; $i < $number; $i++) {
                $data['code'] = $this->create($length);
                $model->insert($data);
                $model->setOption('data', []);
            }
            user_log($this->mid, 'addinvite', 1, '生成邀请码[' . $mem_id . ',' . $level_id . ']' . $number . '个', 'manager');
            $this->success("生成成功", url('member.invite/index'));
        }
        $this->assign('levels', getMemberLevels());
        return $this->fetch();
    }

    /**
     * 转赠
     * @param $id
     * @return mixed
     */
    public function transfer($id, $uid)
    {
        if (empty($id) || empty($uid)) {
            $this->error('参数错误');
        }
        try {
            $count = Db::name("inviteCode")->whereIn('id', idArr($id))->update(['member_id' => $uid]);
        } catch (\Exception $e) {
            $this->error("转赠失败:" . $e->getMessage());
        }
        $this->success("转赠成功", url('member.invite/index'));
    }

    public function lock($id, $is_lock = 1)
    {
        if (empty($id)) {
            $this->error('参数错误');
        }
        try {
            $count = Db::name("inviteCode")->whereIn('id', idArr($id))->update(['is_lock' => $is_lock]);
        } catch (\Exception $e) {
            $this->error('转赠失败:' . $e->getMessage());
        }
        $this->success($is_lock ? '锁定成功' : '解锁成功', url('member.invite/index'));
    }

    /**
     * 生成激活码
     * @param $length
     * @return string
     */
    protected function create($length)
    {
        $c = strtoupper(random_str($length));
        $r = '';
        for ($j = 0; $j < $length; $j += 4) {
            $r .= substr($c, $j, 4) . '-';
        }
        $r = trim($r, '-');
        $exists = Db::name('inviteCode')->where('code', $r)->find();
        if (!empty($exists)) {
            return $this->create($length);
        }
        return $r;
    }
}
