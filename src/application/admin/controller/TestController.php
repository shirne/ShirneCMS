<?php

namespace app\admin\controller;


use app\common\model\AwardLogModel;

/**
 * Class TestController
 * @package app\admin\controller
 */
class TestController extends BaseController
{
    public function index()
    {
        $lists = [];
        $lists[] = [
            'title' => '随机红包',
            'icon' => 'analytics',
            'action' => 'rand_bouns'
        ];

        $this->assign('lists', $lists);
        return $this->fetch();
    }


    public function rand_bouns($amount = 100, $count = 10, $precision = 2, $ratio = 5, $disperse = 10000)
    {
        if ($this->request->isPost()) {
            return redirect(url('', ['amount' => $amount, 'count' => $count, 'precision' => $precision, 'ratio' => $ratio, 'disperse' => $disperse]));
        }

        $total_count = $count;
        $total_amount = $amount;
        $count = 0;
        $amount = [];
        while ($count < $total_count) {
            $curAmount = AwardLogModel::rand_award($total_amount - array_sum($amount), $count, $total_count, $precision, $ratio, $disperse);
            $count++;
            $amount[] = $curAmount;
        }

        $this->assign('amount', $total_amount);
        $this->assign('count', $total_count);
        $this->assign('precision', $precision);
        $this->assign('ratio', $ratio);
        $this->assign('disperse', $disperse);
        $this->assign('amounts', $amount);
        return $this->fetch();
    }
}
