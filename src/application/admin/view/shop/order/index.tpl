{extend name="public:base" /}

{block name="body"}

{include file="public/bread" menu="shop_order_index" title="订单列表" /}

<div id="page-wrapper">

    <div class="row">
        <div class="col-5">
            <div class="btn-toolbar list-toolbar" role="toolbar" aria-label="Toolbar with button groups">
                <div class="btn-group btn-group-sm mr-2" role="group" aria-label="check action group">
                    <a href="javascript:" class="btn btn-outline-secondary checkall-btn" data-toggle="button"
                        aria-pressed="false">全选</a>
                    <a href="javascript:" class="btn btn-outline-secondary checkreverse-btn">反选</a>
                </div>
                <div class="btn-group btn-group-sm mr-2" role="group" aria-label="action button group">
                    <a href="javascript:" class="btn btn-outline-secondary action-btn" data-action="setStatus">设置状态</a>
                    <a href="javascript:" class="btn btn-outline-secondary action-btn"
                        data-action="delete">{:lang('Delete')}</a>
                </div>
                <div class="btn-group btn-group-sm mr-2" role="group" aria-label="Button group with nested dropdown">
                    <button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        导出订单
                    </button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                        <a class="dropdown-item action-btn" data-action="export" href="javascript:"
                            target="_blank">导出选中项</a>
                        <a class="dropdown-item" href="{:url('shop.order/export',['order_ids'=>$orderids])}"
                            target="_blank">导出本页</a>
                        <a class="dropdown-item" href="{:url('shop.order/export',['status'=>1])}"
                            target="_blank">导出未处理</a>
                        <a class="dropdown-item"
                            href="{:url('shop.order/export',['status'=>$status,'start_date'=>$start_date,'end_date'=>$end_date,'audit'=>$audit,'keyword'=>base64_encode($keyword)])}"
                            target="_blank">导出筛选结果</a>
                    </div>
                </div>
                <div class="btn-group btn-group-sm" role="group" aria-label="Button group with nested dropdown">
                    <button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        导出发货单
                    </button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                        <a class="dropdown-item action-btn" data-action="export" data-mode="express" href="javascript:"
                            target="_blank">导出选中项</a>
                        <a class="dropdown-item"
                            href="{:url('shop.order/export',['order_ids'=>$orderids,'mode'=>'express'])}"
                            target="_blank">导出本页</a>
                        <a class="dropdown-item" href="{:url('shop.order/export',['status'=>1,'mode'=>'express'])}"
                            target="_blank">导出未处理</a>
                        <a class="dropdown-item"
                            href="{:url('shop.order/export',['status'=>$status,'start_date'=>$start_date,'end_date'=>$end_date,'audit'=>$audit,'keyword'=>base64_encode($keyword),'mode'=>'express'])}"
                            target="_blank">导出筛选结果</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-7">
            <form action="{:url('shop.order/index')}" method="post">
                <div class="form-row">
                    <div class="col-2 form-group">
                        <select name="audit" class="form-control form-control-sm">
                            <option value="">全部</option>
                            <option value="1" {$audit==='1' ?' selected':''}>已审核</option>
                            <option value="0" {$audit==='0' ?' selected':''}>未审核</option>
                        </select>
                    </div>
                    <div class="col-2 form-group">
                        <select name="status" class="form-control form-control-sm">
                            <option value="">全部</option>
                            <option value="1" {$status==='1' ?' selected':''}>待发货</option>
                            <option value="2" {$status==='2' ?' selected':''}>待收货</option>
                            <option value="3" {$status==='3' ?' selected':''}>待评价</option>
                            <option value="4" {$status==='4' ?' selected':''}>已完成</option>
                            <option value="-1" {$status==='-1' ?' selected':''}>已失效</option>
                        </select>
                    </div>
                    <div class="form-group col input-group input-group-sm date-range">
                        <div class="input-group-prepend">
                            <span class="input-group-text">下单时间</span>
                        </div>
                        <input type="text" class="form-control fromdate" name="start_date" placeholder="选择开始日期"
                            value="{$start_date}">
                        <div class="input-group-middle"><span class="input-group-text">-</span></div>
                        <input type="text" class="form-control todate" name="end_date" placeholder="选择结束日期"
                            value="{$end_date}">
                    </div>
                    <div class="col form-group input-group input-group-sm">
                        <input type="text" class="form-control" name="keyword" value="{$keyword}" placeholder="输入关键词搜索">
                        <span class="input-group-append">
                            <button class="btn btn-outline-secondary" type="submit"><i
                                    class="ion-md-search"></i></button>
                        </span>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <table class="table table-hover table-striped">
        <thead>
            <tr>
                <th width="50">编号</th>
                <th>商品</th>
                <th>订单编号/购买时间</th>
                <th>会员</th>
                <th>价格/返奖额</th>
                <th width="110">状态</th>
                <th width="160">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            {php}$empty=list_empty(7);{/php}
            {volist name="lists" id="v" empty="$empty"}
            <tr>
                <td><input type="checkbox" name="id" value="{$v.order_id}" /><br />{$v.order_id}</td>
                <td>
                    {volist name="v['products']" id="p"}
                    <div class="media">
                        <img class="media-object mr-2 rounded" width="50"
                            src="{$p['product_image']|default='/static/images/nopic.png'}" alt="{$p['product_title']}">
                        <div class="media-body">
                            <h5 class="media-heading">{$p['product_title']}</h5>
                            <div>￥{$p['product_price']} &times; {$p['count']}件</div>
                        </div>
                    </div>
                    {/volist}
                </td>
                <td>{$v.order_no}<br /><br /><span class="text-muted">{$v.create_time|showdate}</span></td>
                <td>
                    <div class="media">
                        {if !empty($v['avatar'])}
                        <img src="{$v.avatar}" class="mr-2 rounded" width="30" />
                        {/if}
                        <div class="media-body">
                            <h5 class="mt-0 mb-1" style="font-size:13px;">
                                {if !empty($v['nickname'])}
                                {$v.nickname}
                                {else/}
                                {$v.username}
                                {/if}
                            </h5>
                            <div style="font-size:12px;">
                                [{$v.member_id} {$levels[$v['level_id']]['level_name']}]
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    {$v.payamount} {if $v['status'] == 0}<a href="javascript:" class="reprice" data-id="{$v.order_id}"
                        data-price="{$v['payamount']}" title="改价"><i class="ion-md-create"></i> </a> {/if}<br />
                    {if $v['rebate_total'] > 0}
                    <a href="{:url('member/award_log',['orderid'=>$v['order_id']])}">{$v.rebate_total}</a>
                    {else/}
                    {$v.rebate_total}
                    {/if}
                </td>
                <td>
                    {$v.status|order_status|raw}
                    {if $v['isaudit'] == 0}
                    <span class="badge badge-warning">待审核</span>
                    {/if}
                    {if $v['rebated'] == 1}
                    <span class="badge badge-success">已返奖</span>
                    {else/}
                    <span class="badge badge-warning">未返奖</span>
                    {/if}
                    {if $v['payedamount'] == 0}
                    <br /><span class="badge">已付: {$v['payedamount']}</span>
                    {/if}
                </td>
                <td class="operations">
                    <a class="btn btn-outline-primary" title="详情"
                        href="{:url('shop.order/detail',array('id'=>$v['order_id']))}"><i class="ion-md-document"></i>
                    </a>

                    {if $v['status'] == 0}
                    <a class="btn btn-outline-danger btn-status" title="取消订单" href="javascript:" data-id="{$v.order_id}"
                        data-status="-1"><i class="ion-md-close-circle-outline"></i> </a>
                    <a class="btn btn-outline-warning btn-status" title="设置支付状态" href="javascript:"
                        data-id="{$v.order_id}" data-status="1"><i class="ion-md-wallet"></i> </a>
                    {elseif $v['status'] == 1 /}
                    <a class="btn btn-outline-info btn-status" title="发货" href="javascript:" data-id="{$v.order_id}"
                        data-status="2" data-express="{$v.express_code}/{$v.express_no}"><i class="ion-md-train"></i>
                    </a>
                    {elseif $v['status'] == 2 /}
                    <a class="btn btn-outline-secondary btn-status" title="修改发货信息" href="javascript:"
                        data-id="{$v.order_id}" data-status="2" data-express="{$v.express_code}/{$v.express_no}"><i
                            class="ion-md-subway"></i> </a>
                    <a class="btn btn-outline-success btn-status" title="收货" href="javascript:" data-id="{$v.order_id}"
                        data-status="3"><i class="ion-md-exit"></i> </a>
                    {elseif $v['status'] == 3 /}
                    <a class="btn btn-outline-success btn-status" title="完成" href="javascript:" data-id="{$v.order_id}"
                        data-status="4"><i class="ion-md-checkbox-outline"></i> </a>
                    {/if}

                    {if $v['rebated'] != 1}
                    {if $v['isaudit'] == 1}
                    <a class="btn btn-outline-warning btn-audit" title="取消审核" href="javascript:" data-id="{$v.order_id}"
                        data-status="0"><i class="ion-md-remove-circle"></i> </a>
                    {else/}
                    <a class="btn btn-outline-success btn-audit" title="审核" href="javascript:" data-id="{$v.order_id}"
                        data-status="1"><i class="ion-md-checkmark-circle"></i> </a>
                    {/if}
                    {/if}
                    <a class="btn btn-outline-danger link-confirm" title="删除" data-confirm="您真的确定要删除吗？\n删除后将不能恢复!"
                        href="{:url('shop.order/delete',array('id'=>$v['order_id']))}"><i class="ion-md-trash"></i> </a>
                </td>
            </tr>
            {/volist}
        </tbody>
    </table>
    {$page|raw}
</div>
{/block}
{block name="script"}
{include file="shop/order/_status_tpl" /}
<script type="text/javascript">
    (function (w) {
        w.actionSetStatus = function (ids) {
            dialog.action(['设为已发货(无物流)', '设为已收货', '设为已完成'], function (idx) {
                var loading = dialog.loading('正在处理');
                $.ajax({
                    url: "{:url('setstatus')}",
                    dataType: 'json',
                    type: 'POST',
                    data: {
                        ids: ids.join(','),
                        status: idx + 2
                    },
                    success: function (json) {
                        loading.close()
                        if (json.code == 1) {
                            dialog.success(json.msg)
                            setTimeout(function () {
                                location.reload();
                            }, 800)
                        } else {
                            dialog.error(json.msg)
                        }
                    },
                    error: function () {
                        loading.close()
                        dialog.error('服务器错误')
                    }
                })
            });
        }
        w.actionExport = function (ids) {
            var baseUrl = "{:url('shop.order/export',['order_ids'=>'__IDS__','mode'=>'__MODE__'])}";
            var idstr = ids.join(',')
            var mode = $(this).data('mode')
            location.href = baseUrl.replace('__IDS__', idstr).replace('__MODE__', mode ? mode : '')
        }
    })(window);
    jQuery(function () {


        var tpl2 = '<div class="row" style="margin:0 20%;">' +
            '<div class="col form-group"> <select class="form-control status-id"><option value="0">待审核</option><option value="1">已审核</option></select></div>' +
            '</div>';
        $('.btn-audit').click(function () {
            var id = $(this).data('id');
            var status = $(this).data('status');
            var dlg = new Dialog({
                onshown: function (body) {
                    var select = body.find('select.status-id');
                    select.val(status);
                },
                onsure: function (body) {
                    $.ajax({
                        url: '{:url("audit")}',
                        type: 'POST',
                        data: {
                            id: id,
                            status: body.find('select.status-id').val(),
                        },
                        dataType: 'JSON',
                        success: function () {
                            dlg.hide();
                            location.reload();
                        }
                    })
                }
            }).show(tpl2, '订单审核');
        });

        $('.reprice').click(function (e) {
            var id = $(this).data('id');
            var orig_price = $(this).data('price')
            var dlg = dialog.prompt('当前价格：' + orig_price, function (input) {
                $.ajax({
                    url: '{:url("reprice")}',
                    type: 'POST',
                    data: {
                        id: id,
                        price: input,
                    },
                    dataType: 'JSON',
                    success: function (json) {
                        if (json.code == 1) {
                            dlg.hide();
                            location.reload();
                        } else {
                            dialog.error(json.msg)
                        }
                    }
                })
                return false;
            })
        })
    });
</script>

{/block}