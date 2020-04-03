{extend name="public:base" /}
{block name="body"}
    <div class="container">
        <div class="page-header">
            <div class="row">
                <h1 class="col-4">收货地址</h1>
                <div class="col-8 mt-3 mb-2 text-right"><a class="btn btn-outline-primary btn-confirm" href="{:aurl('index/member.address/add')}" >添加地址</a></div>
            </div>
        </div>
        <ul class="list-group">
            {foreach name="addresses" item="v"}
                <li class="list-group-item">
                    <div>
                        {if $v.is_default}<span class="float-right badge badge-info">默认</span>{/if}
                        <span class="text-dark">{$v.recive_name}</span>  /  <span class="text-secondary">{$v.mobile}</span>
                    </div>
                    <div>
                        {$v.province}&nbsp;{$v.city}&nbsp;{$v.area}&nbsp;{$v.address}
                    </div>
                    <div class="order-btns text-right">
                        <a class="btn btn-outline-secondary btn-confirm"  href="{:aurl('index/member.address/add',array('id'=>$v['address_id']))}">编辑</a>
                    </div>
                </li>
            {/foreach}
        </ul>
    </div>
{/block}