<?php

namespace app\api\controller\member;

use app\api\controller\AuthedController;
use app\common\model\AwardLogModel;
use app\common\model\MemberAgentModel;
use app\common\model\MemberAuthenModel;
use app\common\model\MemberModel;
use app\common\model\OrderModel;
use think\Db;
use think\response\Json;

/**
 * 代理相关操作
 * @package app\api\controller\member
 */
class AgentController extends AuthedController
{
    public function initialize()
    {
        parent::initialize();
        if (!$this->user['is_agent']) {
            $this->error('您还不是代理，请先升级为代理');
        }
    }

    /**
     * 代理统计信息
     * @return Json 
     */
    public function generic()
    {
        $data = [];
        $data['order_count'] = Db::name('awardLog')->where('member_id', $this->user['id'])
            ->where('create_time', 'gt', strtotime('today -7 days'))
            ->where('status', 'gt', -1)
            ->distinct(true)->field('order_id')->count();
        $data['amount_future'] = Db::name('awardLog')->where('member_id', $this->user['id'])
            ->where('status', 0)->sum('amount');

        $data['total_award'] = Db::name('awardLog')->where('member_id', $this->user['id'])
            ->where('status', 1)->sum('amount');
        $data['store'] = Db::name('Store')->where('member_id', $this->user['id'])->find();
        return $this->response($data);
    }

    /**
     * 升级申请
     * @param int $level_id 
     * @return Json 
     */
    public function upgrade($level_id = 2)
    {
        $authen = MemberAuthenModel::where('level_id', $level_id)
            ->where('member_id', $this->user['id'])
            ->find();
        if ($this->request->isPost()) {
            if ($authen['status'] == 1) {
                $this->error('申请已审核通过,不能修改');
            }
            $data = $this->request->only(['realname', 'mobile', 'province', 'city']);
            try {
                $data['status'] = -1;
                if (empty($authen)) {
                    $data['member_id'] = $this->user['id'];
                    $data['level_id'] = $level_id;
                    MemberAuthenModel::insert($data);
                } else {
                    $authen->save($data);
                }
            } catch (\Exception $err) {
                $this->error('保存失败: %s', [$err->getMessage()]);
            }
            $this->error('申请已提交');
        }
        return $this->response([
            'authen' => $authen
        ]);
    }

    /**
     * 代理分享海报
     * @param string $page 
     * @return Json 
     */
    public function poster($page = 'pages/index/index')
    {

        $platform = $this->request->tokenData['platform'];

        try {
            $userModel = MemberModel::where('id', $this->user['id'])->find();
            $url = $userModel->getSharePoster($platform . '-' . $this->request->tokenData['appid'], $page);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        if (!$url) {
            $this->error($userModel->getError());
        }

        $qrurl = str_replace('-' . $platform . '.jpg', '-qrcode.png', $url);
        return $this->response(['poster_url' => $url, 'qr_url' => $qrurl]);
    }

    /**
     * 代理排行
     * @param string $mode 
     * @return Json 
     */
    public function rank($mode = 'month')
    {

        $list = AwardLogModel::ranks($mode);

        return $this->response(['ranks' => $list]);
    }

    /**
     * 佣金明细
     * @param string $type 
     * @param string $status 
     * @param string $daterange 
     * @return Json 
     */
    public function award_log($type = '', $status = '', $daterange = '')
    {
        $model = Db::view('awardLog mlog', '*')
            ->view('Member m', ['level_id', 'is_agent', 'nickname', 'avatar'], 'm.id=mlog.from_member_id', 'LEFT')
            ->where('mlog.member_id', $this->user['id']);

        $static = Db::name('awardLog')->where('member_id', $this->user['id']);
        if (!empty($type) && $type != 'all') {
            $model->where('mlog.type', $type);
            $static->where('type', $type);
        }
        if ($status !== '') {
            $model->where('mlog.status', $status);
            $static->where('status', $status);
        }
        if ($daterange != '') {
            $end_date = 0;
            if ($daterange == 'week') {
                $start_date = strtotime('last Monday');
            } elseif ($daterange == 'month') {
                $start_date = strtotime('first day of this month midnight');
            } elseif (preg_match('/^(\d{4})\-(\d{1,2})$/', $daterange, $matches)) {
                $start_date = strtotime($daterange);
                $month = $matches[2] + 1;
                $year = $matches[1];
                if ($month > 12) {
                    $month = 1;
                    $year += 1;
                }
                $end_date = strtotime($year . '-' . $month) - 1;
            } else {
                $dateranges = explode('~', $daterange);
                $start_date = strtotime($dateranges[0]);
                if (isset($dateranges[1])) $end_date = strtotime($dateranges[1]);
            }
            if ($start_date > 0) {
                if ($end_date > 0) {
                    $static->whereBetween('create_time', [$start_date, $end_date]);
                    $model->whereBetween('mlog.create_time', [$start_date, $end_date]);
                } else {
                    $static->where('create_time', '>=', $start_date);
                    $model->where('mlog.create_time', '>=', $start_date);
                }
            }
        }

        $logs = $model->order('mlog.id DESC')->paginate(10);
        $static_data = $static->field('count(distinct order_id) as order_count, sum(amount) as total_amount')->find();

        $types = getLogTypes(false, 'award');
        $level = $this->userLevel();
        if ($level['partner_sale_award'] <= 0) {
            unset($types['partner_sale']);
        }
        if ($level['partner_new_award'] <= 0) {
            unset($types['partner_share']);
        }

        return $this->response([
            'types' => $types,
            'static_data' => $static_data,
            'logs' => $logs->items(),
            'total' => $logs->total(),
            'total_page' => $logs->lastPage(),
            'page' => $logs->currentPage()
        ]);
    }
    public function areamember($keyword = '')
    {
        if ($this->user['is_agent'] > 2) {
            $levels = getMemberLevels();
            $model = Db::name('Member')->where('status', 1);
            if (empty($this->user['agent_province'])) {
                $this->user['agent_province'] = $this->user['province'];
                if (empty($this->user['agent_province'])) {
                    $this->user['agent_province'] = '-';
                }
            }
            if (empty($this->user['agent_city'])) {
                $this->user['agent_city'] = $this->user['city'];
                if (empty($this->user['agent_city'])) {
                    $this->user['agent_city'] = '-';
                }
            }
            if (empty($this->user['agent_county'])) {
                $this->user['agent_county'] = $this->user['county'];
                if (empty($this->user['agent_county'])) {
                    $this->user['agent_county'] = '-';
                }
            }
            if ($this->user['is_agent'] <= 4) {
                $model->where('province', $this->user['agent_province']);
            }
            if ($this->user['is_agent'] <= 3) {
                $model->where('city', $this->user['agent_city']);
            }
            $total = $model->count();
            if (!empty($keyword)) {
                $model->whereLike('nickname|mobile', "%$keyword%");
            }
            $users = $model->field('id,username,nickname,level_id,mobile,avatar,gender,is_agent,province,city,county')
                ->order('create_time DESC')->paginate(10);

            if (!empty($users) && !$users->isEmpty()) {
                $uids = array_column($users->items(), 'id');
                $soncounts = [];
                if (!empty($uids)) {
                    $sondata = Db::name('Member')->where('referer', 'in', $uids)
                        ->group('referer')->field('referer,COUNT(id) as count_member')->select();
                    $soncounts = [];
                    foreach ($sondata as $row) {
                        $soncounts[$row['referer']] = $row['count_member'];
                    }
                }

                $agents = MemberAgentModel::getCacheData();

                $users->each(function ($item) use ($soncounts, $levels, $agents) {
                    $item['mobile'] = maskphone($item['mobile']);
                    if (isset($soncounts[$item['id']])) {
                        $item['soncount'] = $soncounts[$item['id']];
                    } else {
                        $item['soncount'] = 0;
                    }
                    if (isset($levels[$item['level_id']])) {
                        $item['level_name'] = $levels[$item['level_id']]['level_name'] ?: '-';
                        $item['level_style'] = $levels[$item['level_id']]['style'] ?: '-';
                    }
                    if (isset($agents[$item['is_agent']])) {
                        $item['agent_name'] = $agents[$item['is_agent']]['name'] ?: '-';
                        $item['agent_short_name'] = $agents[$item['is_agent']]['short_name'] ?: '-';
                        $item['agent_style'] = $agents[$item['is_agent']]['style'] ?: '-';
                    }
                    return $item;
                });
            }
            return $this->response([
                'users' => $users->items(),
                'total' => $users->total(),
                'totalcount' => $total,
                'page' => $users->currentPage()
            ]);
        }

        return $this->response([
            'users' => [],
            'total' => 0,
            'page' => 1
        ]);
    }
    public function areaorder($status = '', $pagesize = 10)
    {
        $level = $this->userLevel();

        $model = Db::view('Order', 'order_id,order_no,member_id,status,payamount,type,create_time')
            ->view('member', ['nickname', 'avatar', 'level_id', 'is_agent', 'mobile'], 'Order.member_id=member.id')
            ->where('Order.province', $this->user['agent_province'])
            ->where('Order.delete_time', 0);
        if ($status !== '') {
            $model->where('Order.status', intval($status));
        } else {
            $model->where('Order.status', 'EGT', 0);
        }

        if ($this->user['is_agent'] == 2) {
            $model->where('Order.city', $this->user['agent_city']);
        }
        $orders = $model->order('Order.status ASC,Order.create_time DESC')->paginate($pagesize);
        if (!empty($orders) && !$orders->isEmpty()) {
            $order_ids = array_column($orders->items(), 'order_id');
            $products = Db::view('OrderProduct', '*')
                ->view('Product', ['id' => 'orig_product_id', 'update_time' => 'orig_product_update'], 'OrderProduct.product_id=Product.id', 'LEFT')
                ->view('ProductSku', ['sku_id' => 'orig_sku_id', 'price' => 'orig_product_price'], 'ProductSku.sku_id=OrderProduct.sku_id', 'LEFT')
                ->whereIn('OrderProduct.order_id', $order_ids)
                ->select();
            $products = array_index($products, 'order_id', true);

            $awards = Db::name('awardLog')->where('member_id', $this->user['id'])->whereIn('order_id', $order_ids)->field('order_id,sum(amount) as commision')->group('order_id')->select();
            $awards = array_column($awards, 'commision', 'order_id');

            $orders->each(function ($item) use ($products, $awards) {
                $item['product_count'] = isset($products[$item['order_id']]) ? array_sum(array_column($products[$item['order_id']], 'count')) : 0;
                $item['products'] = isset($products[$item['order_id']]) ? $products[$item['order_id']] : [];
                if (isset($awards[$item['order_id']])) {
                    $item['commision'] = number_format($awards[$item['order_id']] / 100, 2);
                } else {
                    $item['commision'] = '0.00';
                }
                $item['create_date'] = date('Y-m-d H:i:s', $item['create_time']);
                $item['mobile'] = maskphone($item['mobile']);
                return $item;
            });
        }

        //$counts = OrderModel::getCounts($this->user['id']);
        return $this->response([
            'lists' => $orders->items(),
            'page' => $orders->currentPage(),
            'total' => $orders->total(),
            'total_page' => $orders->lastPage(),
            //'counts'=>$counts
        ]);
    }
    /**
     * 分佣订单明细
     * @param string $status 
     * @param int $pagesize 
     * @return Json 
     */
    public function orders($status = '', $pagesize = 10)
    {
        $level = $this->userLevel();
        $sonids = getMemberSons($this->user['id'], $level['commission_layer']);
        $model = Db::view('Order', 'order_id,order_no,member_id,status,payamount,type,create_time')
            ->view('member', ['nickname', 'avatar', 'level_id', 'is_agent', 'mobile'], 'Order.member_id=member.id')
            ->whereIn('Order.member_id', $sonids)
            ->where('Order.delete_time', 0);
        if ($status !== '') {
            $model->where('Order.status', intval($status));
        } else {
            $model->where('Order.status', 'EGT', 0);
        }
        $orders = $model->order('Order.status ASC,Order.create_time DESC')->paginate($pagesize);
        if (!empty($orders) && !$orders->isEmpty()) {
            $order_ids = array_column($orders->items(), 'order_id');
            $products = Db::view('OrderProduct', '*')
                ->view('Product', ['id' => 'orig_product_id', 'update_time' => 'orig_product_update'], 'OrderProduct.product_id=Product.id', 'LEFT')
                ->view('ProductSku', ['sku_id' => 'orig_sku_id', 'price' => 'orig_product_price'], 'ProductSku.sku_id=OrderProduct.sku_id', 'LEFT')
                ->whereIn('OrderProduct.order_id', $order_ids)
                ->select();
            $products = array_index($products, 'order_id', true);

            $awards = Db::name('awardLog')->where('member_id', $this->user['id'])->whereIn('order_id', $order_ids)->field('order_id,sum(amount) as commision')->group('order_id')->select();
            $awards = array_column($awards, 'commision', 'order_id');

            $orders->each(function ($item) use ($products, $awards) {
                $item['product_count'] = isset($products[$item['order_id']]) ? array_sum(array_column($products[$item['order_id']], 'count')) : 0;
                $item['products'] = isset($products[$item['order_id']]) ? $products[$item['order_id']] : [];
                if (isset($awards[$item['order_id']])) {
                    $item['commision'] = number_format($awards[$item['order_id']] / 100, 2);
                } else {
                    $item['commision'] = '0.00';
                }
                $item['create_date'] = date('Y-m-d H:i:s', $item['create_time']);
                $item['mobile'] = maskphone($item['mobile']);
                return $item;
            });
        }

        //$counts = OrderModel::getCounts($this->user['id']);
        return $this->response([
            'lists' => $orders->items(),
            'page' => $orders->currentPage(),
            'total' => $orders->total(),
            'total_page' => $orders->lastPage(),
            //'counts'=>$counts
        ]);
    }

    /**
     * 获取各种状态订单数量
     * @return Json 
     */
    public function counts()
    {
        $counts = OrderModel::getCounts($this->user['id']);
        return $this->response($counts);
    }

    public function team($pid = 0, $level = 1, $keyword = '')
    {
        $levels = getMemberLevels();
        $curLevel = $levels[$this->user['level_id']];
        $maxlayer = $curLevel['commission_layer'];

        $model = Db::name('Member')->where('status', 1);
        if ($pid == 0) {
            $pid = $this->user['id'];
            if ($level < 2) {
                $model->where('referer', $pid);
            } else {
                if ($level > $maxlayer) {
                    $this->error('您只能查看' . $maxlayer . '层的会员');
                }
                $dbpre = config('database.prefix');
                $where = [];
                $sufix = [];
                while ($level > 1) {
                    $where[] = '`referer` IN( SELECT id FROM `' . $dbpre . 'member` WHERE ';
                    $sufix[] = ')';
                    $level--;
                }
                $condition = implode('', $where) . '`referer`=' . $pid . implode('', $sufix);
                $model->where(Db::raw($condition));
            }
        } elseif ($pid != $this->user['id']) {
            $member = Db::name('Member')->find($pid);
            if (empty($member)) {
                $this->error('会员不存在');
            }
            // if(!$member['is_agent']){
            //     $this->error('会员不是代理');
            // }
            $paths = [$member];
            while ($member['id'] != $this->user['id']) {
                $member = Db::name('Member')->find($member['referer']);
                $paths[] = $member;
                if (count($paths) > $maxlayer) {
                    $this->error('您只能查看' . $maxlayer . '层的会员');
                }
            }
            //$paths=array_reverse($paths);
            //$this->assign('paths',$paths);

            $model->where('referer', $pid);
        }
        $total = $model->count();
        if (!empty($keyword)) {
            $model->whereLike('nickname|mobile', "%$keyword%");
        }
        $users = $model->field('id,username,nickname,level_id,mobile,avatar,gender,is_agent,province,city,county')
            ->order('create_time DESC')->paginate(10);

        if (!empty($users) && !$users->isEmpty()) {
            $uids = array_column($users->items(), 'id');
            $soncounts = [];
            if (!empty($uids)) {
                $sondata = Db::name('Member')->where('referer', 'in', $uids)
                    ->group('referer')->field('referer,COUNT(id) as count_member')->select();
                $soncounts = [];
                foreach ($sondata as $row) {
                    $soncounts[$row['referer']] = $row['count_member'];
                }
            }

            $agents = MemberAgentModel::getCacheData();

            $users->each(function ($item) use ($soncounts, $levels, $agents) {
                $item['mobile'] = maskphone($item['mobile']);
                if (isset($soncounts[$item['id']])) {
                    $item['soncount'] = $soncounts[$item['id']];
                } else {
                    $item['soncount'] = 0;
                }
                if (isset($levels[$item['level_id']])) {
                    $item['level_name'] = $levels[$item['level_id']]['level_name'] ?: '-';
                    $item['level_style'] = $levels[$item['level_id']]['style'] ?: '-';
                }
                if (isset($agents[$item['is_agent']])) {
                    $item['agent_name'] = $agents[$item['is_agent']]['name'] ?: '-';
                    $item['agent_short_name'] = $agents[$item['is_agent']]['short_name'] ?: '-';
                    $item['agent_style'] = $agents[$item['is_agent']]['style'] ?: '-';
                }
                return $item;
            });
        }

        return $this->response([
            'users' => $users->items(),
            'total' => $users->total(),
            'totalcount' => $total,
            'page' => $users->currentPage()
        ]);
    }
}
