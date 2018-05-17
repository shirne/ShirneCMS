<extend name="public:base" />
<block name="body">
    <div class="main-content">
        <div class="page-header"><h1>提现记录</h1></div>
        <ul class="list-group">
            <foreach name="cashes" item="v">
                <li class="list-group-item row">
                    <div class="col-xs-3">{$v.amount|showmoney}<br />实收：{$v.real_amount|showmoney}</div>
                    <div class="col-xs-4">{$v.create_at|showdate}</div>
                    <div class="col-xs-3">{$v.remark}</div>
                    <div class="col-xs-2">{$v.status|o_status}</div>
                </li>
            </foreach>
        </ul>
        {$page}
    </div>
</block>