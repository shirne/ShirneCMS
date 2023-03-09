{extend name="public:base" /}
{block name="body"}
    <div class="container">
        <div class="page-header">
            <div class="row">
                <h1 class="col-4">我的银行卡</h1>
                <div class="col-8 mt-3 mb-2 text-right"><a class="btn btn-outline-primary btn-confirm" href="{:aurl('index/member.account/cardEdit')}" >添加银行卡</a></div>
            </div>
        </div>
        <ul class="list-group">
            {foreach $cards as $key => $v}
                <li class="list-group-item row">
                    <h4>{$v.bank}{$v['is_default']?'<span class="badge badge-info">默认</span>':''}</h4>
                    <div class="help-block">开户行：{$v.bankname}</div>
                    <div class="help-block">开户名：{$v.cardname}</div>
                    <div class="help-block">卡号：{$v.cardno|showcardno}</div>
                    <div class="btn-group">
                        <a href="{:aurl('index/member.account/cardEdit',array('id'=>$v['id']))}">修改</a>
                    </div>
                </li>
            {/foreach}
        </ul>
    </div>
{/block}