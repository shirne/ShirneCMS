<extend name="public:base" />
<block name="body">
    <div class="container">
        <div class="page-header"><h1>提现记录</h1></div>
        <ul class="list-group">
            <php>$empty='<span class="col-12 empty">暂时没有记录</span>';</php>
            <foreach name="cashes" empty="$empty" item="v">
                <li class="list-group-item row">
                    <div class="col-3">{$v.amount|showmoney}<br />实收：{$v.real_amount|showmoney}</div>
                    <div class="col-4">{$v.create_time|showdate}<br />{$v.audit_time|showdate}</div>
                    <div class="col-3">{$v.remark}</div>
                    <div class="col-2">{$v.status|audit_status|raw}</div>
                </li>
            </foreach>
        </ul>
        {$page|raw}
    </div>
</block>