<extend name="public:base" />
<block name="body">
    <div class="container">
        <div class="page-header"><h1>充值记录</h1></div>
        <div class="page-content">
            <ul class="list-group">
                <php>$empty='<span class="col-12 empty">暂时没有记录</span>';</php>
                <foreach name="recharges" empty="$empty" item="v">
                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-3">{$v.amount|showmoney}</div>
                            <div class="col-4">{$v.create_time|showdate}<br />{$v.audit_time|showdate}</div>
                            <div class="col-3">{$v.remark}</div>
                            <div class="col-2">
                                <if condition="$v['paytype_id'] EQ -1">
                                    <if condition="$v['status'] EQ 1">
                                        <span class="badge badge-success">支付成功</span>
                                        <elseif condition="$v['status'] EQ 2"/>
                                        <span class="badge badge-secondary">已取消</span>
                                        <else/>
                                        <span class="badge badge-warning">待支付</span>
                                    </if>
                                    <else/>
                                    {$v.status|audit_status|raw}
                                </if>
                            </div>
                        </div>
                        <if condition="$v['paytype_id'] EQ -1">
                            <if condition="$v['status'] EQ 0">
                                <div class="text-right">
                                    <a href="{:url('index/member/recharge_cancel',['order_id'=>$v['id']])}" class="btn btn-outline-secondary btn-sm mr-2">取消订单</a>
                                    <a href="{:url('index/order/wechatpay',['order_id'=>'CZ_'.$v['id']])}" class="btn btn-outline-danger btn-sm">重新支付</a>
                                </div>
                            </if>
                        </if>
                    </li>
                </foreach>
            </ul>
            {$page|raw}
        </div>
    </div>
</block>