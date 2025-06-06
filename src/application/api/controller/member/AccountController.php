<?php


namespace app\api\controller\member;


use app\api\controller\AuthedController;
use app\common\model\MemberCashinModel;
use app\common\model\MemberOauthModel;
use app\common\validate\MemberCardValidate;
use app\common\model\WechatModel;
use app\common\validate\MemberAuthenValidate;
use extcore\traits\Upload;
use shirne\common\ValidateHelper;
use think\Db;
use think\response\Json;

/**
 * 会员账号相关操作
 * @package app\api\controller\member
 */
class AccountController extends AuthedController
{
    use Upload;

    public function authen()
    {
        $authen = Db::name('memberAuthen')->where('member_id', $this->user['id'])->find();
        if ($this->request->isPost()) {
            if ($authen['status'] > 0) {
                $this->error('您的认证已通过');
            }
            $data = $this->request->only(['realname', 'id_no', 'image', 'image2']);
            $validate = new MemberAuthenValidate();
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }
            $data['update_time'] = time();
            $data['status'] = -1;
            if (empty($authen['id'])) {
                $data['member_id'] = $this->user['id'];
                $data['create_time'] = time();
                Db::name('memberAuthen')->insert($data);
            } else {
                Db::name('memberAuthen')->where('id', $authen['id'])->update($data);
            }
            $this->success('申请已提交');
        }
        return $this->response([
            'authen' => $authen,
        ]);
    }

    /**
     * 会员银行卡列表
     * @return Json 
     */
    public function cards()
    {
        $cards = Db::name('MemberCard')->where('member_id', $this->user['id'])->limit(20)->select();

        return $this->response([
            'cards' => $cards,
            'banklist' => banklist()
        ]);
    }

    /**
     * 会员银行卡详细
     * @param int $id 
     * @return Json 
     */
    public function card_view($id)
    {
        $card = Db::name('MemberCard')->where('id', $id)
            ->where('member_id', $this->user['id'])->find();
        if (empty($card)) {
            $this->error('银行卡资料不存在');
        }

        return $this->response([
            'card' => $card,
            'banklist' => banklist()
        ]);
    }

    /**
     * 提交银行卡资料
     * @param array $card cardno,bankname,cardname,bank,is_default
     * @param int $id
     */
    public function card_save($card, $id = 0)
    {

        $card['is_default'] = empty($card['is_default']) ? 0 : 1;
        $validate = new MemberCardValidate();

        if (!$validate->check($card)) {
            $this->error($validate->getError());
        }

        if ($id > 0) {
            Db::name('MemberCard')->where('id', $id)->update($card);
        } else {
            $count = Db::name('MemberCard')->where('member_id', $this->user['id'])->count();
            if ($count >= 20) {
                $this->error('只能添加20张银行卡信息');
            }
            $card['member_id'] = $this->user['id'];
            $id = Db::name('MemberCard')->insert($card, false, true);
        }
        if ($card['is_default']) {
            Db::name('MemberCard')->where('id', 'NEQ', $id)
                ->where('member_id', $this->user['id'])
                ->update(array('is_default' => 0));
        }
        $this->success('保存成功');
    }

    /**
     * 获取充值信息列表
     * @return Json 
     */
    public function recharge_types()
    {
        $types = Db::name('paytype')->where('status', 1)->order('id ASC')->select();
        return $this->response([
            'types' => $types
        ]);
    }

    /**
     * 提交充值信息
     * TODO 优化在线支付的设置
     * @return mixed
     */
    public function recharge()
    {
        $hasRecharge = Db::name('memberRecharge')->where('status', 0)
            ->where('member_id', $this->user['id'])->find();

        if ($hasRecharge > 0) {
            $this->error('您有充值申请正在处理中');
        }
        $data = $this->request->only('amount,type_id');
        $amount = $data['amount'] * 100;
        $type = $data['type_id'];
        $pay_bill = '';
        if ($type == 'wechat') {
            $typeid = -1;
        } else {

            $typeid = intval($data['type_id']);
            $paytype = Db::name('paytype')->where('status', 1)->where('id', $typeid)->find();
            if (empty($paytype)) {
                $this->error('充值方式错误');
            }

            $uploaded = $this->_upload('recharge', 'pay_bill');
            if (!$uploaded) {
                $this->error($this->uploadError);
            }
            $pay_bill = $uploaded['url'];
        }
        $platform = $this->request->tokenData['platform'] ?: '';
        $data = array(
            'member_id' => $this->user['id'],
            'platform' => $platform,
            'amount' => $amount,
            'paytype_id' => $typeid,
            'pay_bill' => $pay_bill,
            'create_time' => time(),
            'status' => 0,
            'remark' => $_POST['remark']
        );
        if (empty($data['amount']) || $data['amount'] < $this->config['recharge_limit']) {
            $this->error('充值金额填写错误');
        }
        if ($this->config['recharge_power'] > 0 && $data['amount'] % $this->config['recharge_power'] > 0) {
            $this->error('充值金额必需是' . $this->config['recharge_power'] . '的倍数');
        }

        $addid = Db::name('memberRecharge')->insert($data, false, true);
        if ($addid) {
            if ($type == 'wechat') {
                $this->success(['order_id' => 'CZ_' . $addid], 1, '订单已生成，请支付');
            } else {
                $this->success('充值申请已提交');
            }
        }
        $this->error('提交失败');
    }

    /**
     * 充值明细
     * @return Json 
     */
    public function recharge_list()
    {
        $model = Db::name('memberRecharge')->where('member_id', $this->user['id']);

        $recharges = $model->order('id DESC')->paginate(15);

        return $this->response([
            'recharges' => $recharges->items(),
            'total' => $recharges->total(),
            'page' => $recharges->currentPage(),
            'total_page' => $recharges->lastPage()
        ]);
    }

    /**
     * 取消充值
     * @param mixed $order_id 
     * @return void 
     */
    public function recharge_cancel($order_id)
    {
        $result = Db::name('memberRecharge')->where('id', $order_id)->update(['status' => 2]);
        if ($result) {
            $this->success('取消成功');
        } else {
            $this->error('取消失败');
        }
    }

    /**
     * 获取提现配置
     * @return Json 
     */
    public function cash_config()
    {
        $wechats = WechatModel::where('account_type', 'service')->select();
        $user = $this->user;
        return $this->response([
            'types' => $this->config['cash_types'],
            'limit' => $this->config['cash_limit'],
            'max' => $this->config['cash_max'],
            'power' => $this->config['cash_power'],
            'fee' => $this->config['cash_fee'],
            'fee_min' => $this->config['cash_fee_min'],
            'fee_max' => $this->config['cash_fee_max'],
            'wechats' => array_map(function ($item) use ($user) {
                $followed = DB::name('member_oauth')->where('member_id', $user['id'])->where('type_id', $item['id'])->find();
                return [
                    'id' => $item['id'],
                    'type' => $item['type'],
                    'account_type' => $item['account_type'],
                    'hash' => $item['hash'],
                    'title' => $item['title'],
                    'qrcode' => $item['qrcode'],
                    'logo' => $item['logo'],
                    'openid' => $followed['openid'] ?? '',
                    'avatar' => $followed['avatar'] ?? '',
                    'nickname' => $followed['nickname'] ?? '',
                    'is_follow' => empty($followed) || empty($followed['is_follow']) ? 0 : 1
                ];
            }, $wechats->toArray())
        ]);
    }

    /**
     * 提现明细
     * @param string $status 
     * @return Json 
     */
    public function cash_list($status = '')
    {
        $model = Db::name('memberCashin')->where('member_id', $this->user['id']);
        if ($status !== '') {
            $model->where('status', $status);
        }
        $cashes = $model->paginate(15);

        return $this->response([
            'total' => $cashes->total(),
            'cashes' => $cashes->items(),
            'page' => $cashes->currentPage(),
            'total_page' => $cashes->lastPage()
        ]);
    }

    /**
     * 提交提现申请
     * @param double $amount 提金额
     * @param int $card_id 银行卡id
     * @param string $remark 备注
     * @param string $cashtype 提现类型
     * @param string $form_id 小程序中获取的form_id ，用于模板消息发送
     * @return void 
     */
    public function cash()
    {
        $hascash = Db::name('memberCashin')->where('member_id', $this->user['id'])
            ->where('status', 0)->count();
        if ($hascash > 0) {
            $this->error('您有提现申请正在处理中');
        }

        $rdata = $this->request->only('amount,card_id,remark,cashtype,form_id');
        $amount = $rdata['amount'] * 100;
        $remark = $rdata['remark'];

        if (empty($amount) || $amount < $this->config['cash_limit']) {
            $this->error('提现金额填写错误');
        }
        if ($this->config['cash_power'] > 0 && $amount % $this->config['cash_power'] > 0) {
            $this->error('提现金额必需是' . $this->config['cash_power'] . '的倍数');
        }
        if ($amount > $this->user['reward']) {
            $this->error('可提现金额不足');
        }

        $platform = $this->request->tokenData['platform'] ?: '';
        $appid = $this->request->tokenData['appid'] ?: '';
        $cash_fee = round($amount * $this->config['cash_fee'] / 100);
        if ($this->config['cash_fee_min'] > 0 && $cash_fee < $this->config['cash_fee_min'] * 100) {
            $cash_fee = round($this->config['cash_fee_min'] * 100);
        }
        if ($this->config['cash_fee_max'] > 0 && $cash_fee > $this->config['cash_fee_max'] * 100) {
            $cash_fee = round($this->config['cash_fee_max'] * 100);
        }
        $data = array(
            'member_id' => $this->user['id'],
            'platform' => $platform,
            'appid' => $appid,
            'form_id' => $rdata['form_id'],
            'cashtype' => $rdata['cashtype'],
            'amount' => $amount,
            'cash_fee' => $cash_fee,
            'real_amount' => $amount - $cash_fee,
            'create_time' => time(),
            'bank_id' => 0,
            'status' => 0,
            'remark' => $remark
        );
        if ($rdata['cashtype'] == 'wechat') {
            $openid = $this->request->param('openid');
            if (empty($openid)) {
                $appid = $this->request->tokenData['appid'];
                $appraw = Db::name('wechat')->where('appid', $appid)->find();
                if (!empty($appraw)) {
                    $authraw = MemberOauthModel::where('type', $appraw['account_type'])
                        ->where('member_id', $this->user['id'])->find();
                    if (!empty($authraw)) {
                        $openid = $authraw['openid'];
                    }
                }
            } else {
                $authraw = MemberOauthModel::where('openid', $openid)
                    ->where('member_id', $this->user['id'])->find();
                if (!empty($authraw)) {
                    $appraw = Db::name('wechat')->where('id', $authraw['type_id'])->find();
                    if (!empty($appraw)) {
                        $appid = $appraw['appid'];
                    }
                }
            }
            if (empty($openid) || empty($appid)) {
                $this->error('提现微信账户错误');
            }
            $data['cardno'] = $openid;
            $data['card_name'] = $this->request->param('realname');
            $data['bank_name'] = $appid;
        } elseif ($rdata['cashtype'] == 'alipay') {
            $data['card_name'] = $this->request->param('alipay');
        } else {

            $bank_id = intval($rdata['card_id']);

            if (empty($bank_id)) {
                $carddata = $this->request->only('bank,bankname,cardname,cardno');
                if (empty($carddata['bank'])) {
                    $this->error('请填写银行名称');
                }
                if (empty($carddata['bankname'])) {
                    $this->error('请填写开户行名称');
                }
                if (empty($carddata['cardname'])) {
                    $this->error('请填写开户名称');
                }
                if (empty($carddata['cardno'])) {
                    $this->error('请填写卡号');
                }
                if (ValidateHelper::isBankcard($carddata['cardno'])) {
                    $this->error('银行卡号错误');
                }
                $carddata['member_id'] = $this->user['id'];
                $bank_id = Db::name('MemberCard')->insert($carddata, false, true);
            }
            $card = Db::name('MemberCard')->where(array('member_id' => $this->user['id'], 'id' => $bank_id))->find();
            $data['bank_id'] = $bank_id;
            $data['bank'] = $card['bank'];
            $data['bank_name'] = $card['bankname'];
            $data['card_name'] = $card['cardname'];
            $data['cardno'] = $card['cardno'];
        }

        $addid = MemberCashinModel::create($data);
        if ($addid['id']) {
            //money_log($this->user['id'],-$data['amount'],'提现申请扣除','cash',0,'reward');
            //Db::name('member')->where('id',$this->user['id'])->setInc('froze_money',$data['amount']);
            $this->success('提现申请已提交');
        } else {
            $this->error('申请失败');
        }
    }

    /**
     * 余额明细
     * @param string $type 
     * @param string $field 
     * @return Json 
     */
    public function money_log($type = '', $field = '')
    {
        $model = Db::view('MemberMoneyLog mlog', '*')
            ->view('Member m', ['nickname', 'avatar', 'is_agent', 'level_id'], 'm.id=mlog.from_member_id', 'LEFT')
            ->where('mlog.member_id', $this->user['id']);
        if (!empty($type) && $type != 'all') {
            $model->where('mlog.type', $type);
        }
        if (!empty($field) && $field != 'all') {
            $model->where('mlog.field', $field);
        }

        $logs = $model->order('mlog.id DESC')->paginate(10);
        $types = getLogTypes(false);

        return $this->response([
            'types' => $types,
            'logs' => $logs->items(),
            'total' => $logs->total(),
            'page' => $logs->currentPage(),
            'total_page' => $logs->lastPage(),
        ]);
    }

    public function search_member($mobile)
    {
        $member = Db::name('member')->where('mobile', $mobile)->field(['nickname', 'id', 'mobile', 'avatar'])->find();
        if (empty($member)) {
            $this->error('未搜索到会员');
        }
        return $this->response($member);
    }

    public function get_member()
    {
        $ids = Db::name('memberMoneyLog')->where('member_id', $this->user['id'])->whereIn('type', 'transout')->column('from_member_id');
        if (empty($ids)) {
            $this->error('未搜索到会员');
        }
        $members = Db::name('member')->whereIn('id', $ids)->field(['nickname', 'id', 'mobile', 'avatar'])->select();
        if (empty($members)) {
            $this->error('未搜索到会员');
        }
        return $this->response($members);
    }

    /**
     * 会员转账，可转出到其它会员，或多种余额间互转
     * @param string $action 转移类型
     * @param double $field 转移余额类型
     * @param double $member_id 接受会员id
     * @param double $amount 转移金额
     * @return void 
     */
    public function transfer($action)
    {
        $secpassword = $this->request->param('secpassword');
        if (empty($secpassword)) {
            $this->error('请填写安全密码');
        }
        if (!compare_secpassword($this->user, $secpassword)) {
            $this->error('安全密码错误');
        }
        $data = $this->request->only('action,field,member_id,amount');
        $data['amount'] = floatval($data['amount']);
        if ($action == 'transout') {
            if (!in_array($data['field'], ['money', 'credit', 'reward'])) {
                $this->error('转赠积分类型错误');
            }
            $tomember = Db::name('member')->where('id|username|mobile', $data['member_id'])->find();
            if (empty($tomember)) {
                $this->error('会员信息错误');
            }
            if ($data['amount'] <= 0) {
                $this->error('转赠金额错误');
            }
            if ($data['amount'] * 100 > $this->user[$data['field']]) {
                $this->error('您的' . lang('Balance') . '不足');
            }
            money_log($this->user['id'], -$data['amount'] * 100, '转赠给会员' . $tomember['nickname'], 'transout', $tomember['id'], $data['field']);
            money_log($tomember['id'], $data['amount'] * 100, '会员' . $this->user['nickname'] . '转入', 'transin', $this->user['id'], $data['field']);
            if ($data['field'] == 'credit') {
                $this->unfreeze($data['amount']);
            }
            $this->success('转赠成功');
        } elseif ($action == 'transmoney') {
            if (!in_array($data['field'], ['credit', 'reward'])) {
                $this->error('转入积分类型错误');
            }
            if ($data['amount'] <= 0) {
                $this->error('转入金额错误');
            }
            if ($data['amount'] * 100 > $this->user[$data['field']]) {
                $this->error('您的积分不足');
            }
            money_log($this->user['id'], -$data['amount'] * 100, '转入消费积分', 'transout', $this->user['id'], $data['field']);
            money_log($this->user['id'], $data['amount'] * 100, '从' . money_type($data['field'], false) . '转入', 'transin', $this->user['id'], 'money');
            if ($data['field'] == 'credit') {
                $this->unfreeze($data['amount']);
            }
            $this->success('转入成功');
        }
    }

    /**
     * 余额解冻
     * @param mixed $amount 
     * @return true 
     */
    private function unfreeze($amount)
    {
        $amount = $amount * 100;
        $freezes = Db::name('memberFreeze')->where('member_id', $this->user['id'])
            ->where('status', 1)->order('freeze_time ASC,amount ASC,id ASC')->select();
        $unfreezed = 0;
        foreach ($freezes as $freeze) {
            Db::name('memberFreeze')->where('id', $freeze['id'])->update(['status' => 0]);
            $unfreezed += $freeze['amount'];
            if ($unfreezed >= $amount) {
                if ($unfreezed > $amount) {
                    $toFreeze = $unfreezed - $amount;
                    $newData = [
                        'member_id' => $this->user['id'],
                        'award_log_id' => $freeze['award_log_id'],
                        'amount' => $toFreeze,
                        'create_time' => $freeze['create_time'],
                        'freeze_time' => $freeze['freeze_time'],
                        'status' => 1
                    ];
                    Db::name('memberFreeze')->insert($newData);
                }
                break;
            }
        }
        return true;
    }
}
