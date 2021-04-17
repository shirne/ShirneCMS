<extend name="public:base" />

<block name="body">
    <include file="public/bread" menu="credit_order_index" section="积分商城" title="订单管理" />

    <div id="page-wrapper" class="page-form">
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">订单信息</h3>
            </div>
            <table class="table">
                <tbody>
                    <tr>
                        <td>订单号</td>
                        <td>{$model.order_no}</td>
                        <td>下单会员</td>
                        <td>[{$member.id}]{$member.username}</td>
                    </tr>
                    <tr>
                        <td>下单日期</td>
                        <td>{$model.create_time|showdate}</td>
                        <td>订单状态</td>
                        <td>{$model.status|order_status|raw}</td>
                    </tr>
                    <tr>
                        <th colspan="4">订单商品</th>
                    </tr>
                    <tr>
                        <td>
                            <volist name="goodss" id="p">
                                <div class="media">
                                    <div class="media-left">
                                        <img class="media-object" src="{$p['goods_image']}"
                                            alt="{$p['goods_title']}">
                                    </div>
                                    <div class="media-body">
                                        <h4 class="media-heading">{$p['goods_title']}</h4>
                                        <div>￥{$p['goods_price']} &times; {$p['count']}件</div>
                                    </div>
                                </div>
                            </volist>
                        </td>
                    </tr>
                    <if condition="$model['remark']">
                        <tr>
                            <th colspan="4">订单备注</th>
                        </tr>
                        <tr>
                            <td>
                                {$model.remark}
                            </td>
                        </tr>
                    </if>
                    <tr>
                        <th>支付积分</th>
                        <td>
                            {$model.paycredit}
                        </td>
                        <th>支付金额</th>
                        <td>
                            {$model.payamount}
                            <if condition="!empty($model['pay_type'])">
                                <if condition="$model['pay_type'] EQ 'offline'">
                                    <span class="badge badge-warning">线下支付</span>
                                    <elseif condition="$model['pay_type'] EQ 'balance'" />
                                    <span class="badge badge-primary">余额支付</span>
                                    <elseif condition="$model['pay_type'] EQ 'wechat'" />
                                    <span class="badge badge-success">微信支付</span>
                                    <elseif condition="$model['pay_type'] EQ 'alipay'" />
                                    <span class="badge badge-info">支付宝</span>
                                    <else />
                                    <span class="badge badge-secondary">{$model['pay_type']}</span>
                                </if>
                            </if>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <if condition="!empty($payorders)">
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">支付信息</h3>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>支付单号</th>
                            <th>发起时间</th>
                            <th>支付类型</th>
                            <th>支付状态</th>
                            <th>交易号</th>
                            <th>是否有退款</th>
                        </tr>
                    </thead>
                    <tbody>
                        <volist name="payorders" id="po">
                            <tr>
                                <td>{$po.order_no}</td>
                                <td>{$po.create_time|showdate}</td>
                                <td>
                                    <if condition="$po['pay_type'] == 'wechat'">
                                        <span class="badge badge-success has-tooltip"
                                            title="{$po.trade_type}">微信支付</span>
                                        <elseif condition="$po['pay_type'] == 'alipay'" />
                                        <span class="badge badge-info">支付宝</span>
                                        <else />
                                        <span class="badge badge-secondary">{$po.pay_type}</span>
                                    </if>
                                </td>
                                <td>
                                    <if condition="$po['status'] == 1">
                                        <span class="badge badge-success">已支付</span><br />
                                        <span class="badge badge-secondary">{$po.pay_time|showdate}</span>
                                        <elseif condition="$po['status'] LT 0" />
                                        <span class="badge badge-secondary">已失效</span>
                                        <else />
                                        <span class="badge badge-warning has-tooltip paystatus" title="查询支付状态"
                                            data-id="{$po.id}">未支付</span>
                                    </if>

                                </td>
                                <td>{$po.pay_bill}</td>
                                <td>
                                    <if condition="$po['is_refund'] == 1">
                                        <span class="badge badge-warning">有退款</span>
                                        {$po.refund_fee}
                                        <else />
                                        <span class="badge badge-secondary">无退款</span>
                                    </if>
                                </td>
                            </tr>
                        </volist>
                    </tbody>
                </table>
            </div>
        </if>
        <if condition="$model['status'] GT 1">
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">发货信息</h3>
                </div>
                <table class="table">
                    <tbody>
                    <tr>
                        <td>快递公司</td>
                        <td><if condition="!empty($model['express_code'])">[{$model.express_code}]<php>$expresses=config('express.');</php>{$expresses[$model['express_code']]}<else/>无需物流</if></td>
                        <td>快递单号</td>
                        <td>{$model.express_no}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </if>
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">收货信息</h3>
            </div>
            <table class="table">
                <tbody>
                    <tr>
                        <td>收货人</td>
                        <td>{$model.recive_name}</td>
                        <td>电话</td>
                        <td>{$model.mobile}</td>
                    </tr>
                    <tr>
                        <td>地址</td>
                        <td colspan="3">{$model.province} {$model.city} {$model.area} {$model.address}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <if condition="$model['status'] GT -1 AND $model['status'] LT 4">
            <div class="form-group submit-btn">
                <if condition="$model['status'] EQ 0">
                    <a class="btn btn-outline-danger btn-status" title="取消订单" data-id="{$model.order_id}"
                        href="javascript:" data-status="-1"><i class="ion-md-close-circle-outline"></i> 取消订单</a>
                    <a class="btn btn-outline-warning btn-status" title="设置支付状态" data-id="{$model.order_id}"
                        href="javascript:" data-status="1"><i class="ion-md-wallet"></i> 设置支付状态</a>
                <elseif condition="$model['status'] EQ 1" />
                    <a class="btn btn-outline-info btn-status" title="发货" href="javascript:" data-id="{$model.order_id}"
                        data-status="2" data-express="{$model.express_code}/{$model.express_no}"><i
                            class="ion-md-train"></i> 发货</a>
                <elseif condition="$model['status'] EQ 2" />
                    <a class="btn btn-outline-secondary btn-status" title="修改发货信息" href="javascript:"
                        data-id="{$model.order_id}" data-status="2"
                        data-express="{$model.express_code}/{$model.express_no}"><i class="ion-md-subway"></i>
                        修改发货信息</a>
                    <a class="btn btn-outline-success btn-status" title="收货" href="javascript:"
                        data-id="{$model.order_id}" data-status="3"><i class="ion-md-exit"></i> 收货</a>
                <elseif condition="$model['status'] EQ 3" />
                    <a class="btn btn-outline-success btn-status" title="完成" href="javascript:"
                        data-id="{$model.order_id}" data-status="4"><i class="ion-md-checkbox-outline"></i> 完成订单</a>
                </if>
            </div>
        </if>
    </div>
</block>
<block name="script">
    <include file="credit/order/_status_tpl" />
    <script type="text/javascript">
        jQuery(function ($) {


            $('.paystatus').click(function (e) {
                var id = $(this).data('id');
                var loading = dialog.loading();
                $.ajax({
                    url: "{:url('payquery')}",
                    dataType: 'json',
                    data: {
                        payid: id
                    },
                    success: function (json) {
                        loading.close();
                        if (json.code == 1) {
                            dialog.success(json.msg)
                        } else {
                            dialog.error(json.msg)
                        }
                        setTimeout(function () {
                            location.reload()
                        }, 800)
                    }
                })
            });
        })
    </script>
</block>