<extend name="public:base" />
<block name="body">
    <div class="main-content">
        <div class="page-header">
            <div class="pull-right"><a class="btn btn-default btn-confirm" href="{:url('member/cardedit')}" >添加银行卡</a></div>
            <h1>我的银行卡</h1>
        </div>
        <ul class="list-group">
            <foreach name="cards" item="v">
                <li class="list-group-item row">
                    <h4>{$v.bank}{$v['is_default']?'<span class="label label-info">默认</span>':''}</h4>
                    <div class="help-block">开户行：{$v.bankname}</div>
                    <div class="help-block">开户名：{$v.cardname}</div>
                    <div class="help-block">卡号：{$v.cardno|showcardno}</div>
                    <div class="btn-group">
                        <a href="{:url('member/cardedit',array('id'=>$v['id']))}">修改</a>
                    </div>
                </li>
            </foreach>
        </ul>
    </div>
</block>