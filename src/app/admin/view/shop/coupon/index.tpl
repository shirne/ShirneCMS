{extend name="public:base" /}

{block name="body"}

<include file="public/bread" menu="shop_coupon_index" title="优惠券管理" />

<div id="page-wrapper">
    
    <div class="row list-header">
        <div class="col-6">
            <a href="{:url('shop.coupon/add')}" class="btn btn-outline-primary btn-sm"><i class="ion-md-add"></i> 添加优惠券</a>
        </div>
        <div class="col-6">
            <form action="{:url('shop.coupon/index')}" method="post">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" name="key" placeholder="输入优惠券名称搜索">
                    <div class="input-group-append">
                      <button class="btn btn-outline-secondary" type="submit"><i class="ion-md-search"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <table class="table table-hover table-striped">
        <thead>
            <tr>
                <th width="50">编号</th>
                <th>名称</th>
                <th>类型</th>
                <th>适用</th>
                <th>有效期</th>
                <th>数量</th>
                <th>已领</th>
                <th width="160">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
        <php>$empty=list_empty(8);</php>
        {volist name="lists" id="v" empty="$empty"}
            <tr>
                <td>{$v.id}</td>
                <td>{$v.title}</td>
                <td>
                    {if $v['type'] EQ 1}
                        <span class="badge badge-info">折扣券 {$v['discount']}%</span>
                        {else/}
                        <span class="badge badge-warning">满减券 满{$v['limit']}减{$v['amount']}</span>
                    {/if}
                </td>
                <td>
                    {if $v['bind_type'] EQ 1}
                        <span class="badge badge-warning">类目券</span> {$v.category_title}
                    {elseif $v['bind_type'] EQ 2/}
                        <span class="badge badge-primary">品牌券</span> {$v.brand_title}
                    {elseif $v['bind_type'] EQ 3/}
                        <span class="badge badge-info">商品券</span> {$v.product_title}
                    {elseif $v['bind_type'] EQ 4/}
                        <span class="badge badge-dark">规格券</span> {$v.product_title}/{$v.goods_no}
                    {else/}
                        <span class="badge badge-secondary">通用券</span>
                    {/if}
                </td>
                <td>{$v.start_time|showdate}<br />{$v.end_time|showdate}</td>
                <td>{if $v['stock'] LT 0}不限{else/}{$v.stock}{/if}</td>
                <td>{$v.receive}</td>
                <td class="operations">
                    <a class="btn btn-outline-primary" title="编辑" href="{:url('shop.coupon/update',array('id'=>$v['id']))}"><i class="ion-md-create"></i></a>
                    <a class="btn btn-outline-primary" title="领取记录" href="{:url('shop.coupon/itemlist',array('gid'=>$v['id']))}"><i class="ion-md-menu"></i></a>
                    <a class="btn btn-outline-danger link-confirm" data-confirm="您真的确定要删除吗？\n删除后将不能恢复!" title="删除" href="{:url('shop.coupon/delete',array('id'=>$v['id']))}" ><i class="ion-md-trash"></i></a>
                </td>
            </tr>
        {/volist}
        </tbody>
    </table>
    {$page|raw}
</div>
{/block}