<?php

namespace modules\credit;

use app\common\model\PayOrderModel;
use modules\credit\model\CreditOrderModel;
use think\facade\Log;

Log::record('credit init');
PayOrderModel::register('credit', '积分订单', 'CR_', CreditOrderModel::class);
