<?php

namespace app\index\controller\member;


use app\common\validate\MemberMessageValidate;
use think\Db;

/**
 * 消息控制器
 * Class MessageController
 * @package app\index\controller\member
 */
class MessageController extends BaseController
{
    public function index($page = 1, $pagesize = 15)
    {
        $paged = Db::view('MemberMessage', '*')
            ->view('member', ['username' => 'from_username', 'nickname' => 'from_nickname', 'avatar' => 'from_avatar', 'level_id' => 'from_level_id'], 'member.id=MemberMessage.from_member_id', 'LEFT')
            ->where('member_id', $this->userid)
            ->where('is_delete', 0)
            ->where('reply_id', 0)
            ->order('create_time', 'desc')->limit($pagesize * ($page - 1), $pagesize)->paginate();

        $lists = $paged->items();
        foreach ($lists as &$item) {
            if (empty($item['from_nickname'])) {
                $item['from_nickname'] = $item['from_username'];
            }
        }
        $ids = array_column($lists, 'message_id');
        if (!empty($ids)) {
            Db::name('MemberMessage')->where('member_id', $this->userid)->where('show_time', 0)->whereIn('message_id', $ids)->update(['show_time' => time()]);
        }

        $this->assign('lists',  $lists);
        $this->assign('page', $paged->render());
        return $this->fetch();
    }

    public function list($id, $page = 1, $pagesize = 100)
    {
        $id = $id;
        $message = Db::view('MemberMessage', '*')
            ->view('member', ['username' => 'from_username', 'nickname' => 'from_nickname', 'avatar' => 'from_avatar', 'level_id' => 'from_level_id'], 'member.id=MemberMessage.from_member_id', 'LEFT')
            ->view('member to_member', ['username' => 'to_username', 'nickname' => 'to_nickname', 'avatar' => 'to_avatar', 'level_id' => 'to_level_id'], 'member.id=MemberMessage.member_id', 'LEFT')
            ->where('message_id', (int)$id)
            ->where('is_delete', 0)
            ->find();
        if (empty($message) || ($message['member_id'] != $this->user['id'] && $message['from_member_id'] != $this->user['id'])) {
            $this->error('消息已删除');
        }
        if (empty($message['from_nickname'])) {
            $message['from_nickname'] = $message['from_username'];
        }
        if (empty($item['to_nickname'])) {
            $message['to_nickname'] = $message['to_username'];
        }
        $paged = Db::view('MemberMessage', '*')
            ->view('member', ['username' => 'from_username', 'nickname' => 'from_nickname', 'avatar' => 'from_avatar', 'level_id' => 'from_level_id'], 'member.id=MemberMessage.from_member_id', 'LEFT')
            ->view('member to_member', ['username' => 'to_username', 'nickname' => 'to_nickname', 'avatar' => 'to_avatar', 'level_id' => 'to_level_id'], 'member.id=MemberMessage.member_id', 'LEFT')
            ->where('is_delete', 0)
            ->where('group_id', (int)$id)
            ->order('create_time', 'asc')->limit($pagesize * ($page - 1), $pagesize)->paginate();

        $lists = $paged->items();
        foreach ($lists as &$item) {
            if (empty($item['from_nickname'])) {
                $item['from_nickname'] = $item['from_username'];
            }
            if (empty($item['to_nickname'])) {
                $item['to_nickname'] = $item['to_username'];
            }
        }

        $ids = array_column($lists, 'message_id');
        if (!empty($ids)) {
            Db::name('MemberMessage')->where('member_id', $this->userid)->where('show_time', 0)->whereIn('message_id', $ids)->update(['show_time' => time(), 'read_time' => time()]);
        }

        $this->assign('active',  $message['member_id'] == $this->user['id'] ? 0 : 1);
        $this->assign('message',  $message);
        $this->assign('lists',  $lists);
        $this->assign('page', $paged->render());
        return $this->fetch();
    }

    public function sendlist($page = 1, $pagesize = 15)
    {
        $paged = Db::view('MemberMessage', '*')
            ->view('member', ['username' => 'to_username', 'nickname' => 'to_nickname', 'avatar' => 'to_avatar', 'level_id' => 'to_level_id'], 'member.id=MemberMessage.member_id', 'LEFT')
            ->where('from_member_id', $this->userid)->where('reply_id', 0)
            ->where('is_delete', 0)
            ->limit($pagesize * ($page - 1), $pagesize)->paginate();

        $lists = $paged->items();
        foreach ($lists as &$item) {
            if (empty($item['to_nickname'])) {
                $item['to_nickname'] = $item['to_username'];
            }
        }
        $this->assign('lists', $lists);
        $this->assign('page', $paged->render());
        return $this->fetch();
    }

    public function send()
    {
        if ($this->request->isPost()) {
            $data = $this->request->only('member_id,title,content,reply_id', 'post');

            $replyid = isset($data['reply_id']) ? intval($data['reply_id']) : 0;
            $data['reply_id'] = $replyid;
            if ($replyid > 0) {
                $reply = Db::name('MemberMessage')->where('message_id', $replyid)
                    ->where('is_delete', 0)->find();
                if (empty($reply)) {
                    $this->error('消息已删除');
                }
                if ($reply['from_member_id'] == 0) {
                    $this->error('系统消息不支持回复');
                }
                if (empty($reply['group_id'])) {
                    $data['group_id'] = $replyid;
                } else {
                    $data['group_id'] = $reply['group_id'];
                }
                $data['member_id'] = $reply['from_member_id'];
            } else {
                $data['group_id'] = 0;
            }

            $validate = new MemberMessageValidate();
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {

                $data['from_member_id'] = $this->userid;

                $uploaded = $this->_uploadFile('company/' . $this->user['id'], 'attachment');
                if (!empty($uploaded)) {
                    $data['attachment'] = $uploaded['url'];
                } elseif ($this->uploadErrorCode > 102) {
                    $this->error($this->uploadErrorCode . ':' . $this->uploadError);
                }

                $data['create_time'] = time();
                $data['update_time'] = time();

                $id = Db::name('MemberMessage')->insert($data, false, true);
                if ($id) {
                    if (!empty($data['group_id'])) {
                        Db::name('MemberMessage')->where('message_id', $data['group_id'])->update(['update_time' => time()]);
                    }
                    user_log($this->userid, 'sendmessage', 1, '发送消息:' . $id);
                    $this->success('发送成功', aurl('index/member.message/index'), Db::name('MemberMessage')->find($id));
                } else {
                    $this->error('发送失败');
                }
            }
        }


        return $this->fetch();
    }
    public function read($id)
    {
        $id = intval($id);
        Db::name('MemberMessage')->where('member_id', $this->userid)->where('read_time', 0)->where('message_id', $id)->update(['read_time' => time()]);
        $this->success('处理成功');
    }

    public function del($id)
    {
        $id = intval($id);
        $message = Db::name('MemberMessage')->where('message_id', $id)->find();

        Db::name('MemberMessage')->where('member_id', $this->userid)->where('message_id', $id)->delete();
        if ($message['reply_id'] == 0) {
            Db::name('MemberMessage')->where('member_id', $this->userid)->where('group_id', $id)->delete();
        }
        $this->success('删除成功');
    }
}
