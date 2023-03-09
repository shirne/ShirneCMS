{extend name="public:base" /}
{block name="body"}
    <div class="container">
        <div class="page-header">
            <div class="row">
                <h1 class="col-4">发票资料</h1>
                <div class="col-8 mt-3 mb-2 text-right"><a class="btn btn-outline-primary btn-confirm" href="{:aurl('index/member.invoice/add')}" >添加地址</a></div>
            </div>
        </div>
        <ul class="list-group">
            {foreach $invoices as $key => $v}
                <li class="list-group-item">
                    <div>
                        {if $v.is_default}<span class="float-right badge badge-info">默认</span>{/if}
                        <span class="text-dark">{$v.title}</span>
                    </div>
                    <div>
                        {if $v['type']==1}
                            <span class="text-danger">增值税</span>
                            {else/}
                            <span class="text-secondary">普通税</span>
                        {/if}
                    </div>
                    <div class="order-btns text-right">
                        <a class="btn btn-outline-secondary btn-confirm"  href="{:aurl('index/member.invoice/add',array('id'=>$v['id']))}">编辑</a>
                    </div>
                </li>
            {/foreach}
        </ul>
    </div>
{/block}