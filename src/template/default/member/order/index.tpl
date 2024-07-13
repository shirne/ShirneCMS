{extend name="public:base" /}
{block name="body"}
<div class="container">
    <div class="page-header">
        <h1>我的订单</h1>
    </div>
    <div class="list-group">
        {php}$empty='<span class="col-12 empty">您还没有下单哦</span>';{/php}
        {volist name="orders" empty="$empty" id="v"}
        <div class="list-group-item">
            <div style="margin-bottom:10px;">{$v.order_no}
                <div class="float-right">
                    {if $v['status'] == 0}
                    <span class="badge badge-warning">未支付</span>
                    {elseif $v['status'] == 4/}
                    <span class="badge badge-success">已完成</span>
                    {elseif $v['status'] > 0/}
                    {if $v['isaudit'] == 1}
                    <a class="btn btn-default btn-confirm" href="javascript:" data-id="{$v.apply_id}">确认完成</a>
                    {else/}
                    <span class="badge badge-warning">待审核</span>
                    {/if}
                    {else/}
                    <span class="badge badge-default">订单已作废</span>
                    {/if}
                </div>
            </div>
            <div>
                <a href="{:aurl('index/member.order/detail',['id'=>$v['order_id']])}" class="d-block clearfix">
                    {volist name="v.products" id="prod"}
                    <div class="media">
                        <div class="media-left">
                            <img class="media-object" width="50" height="50" src="{$prod['product_image']}" alt="...">
                        </div>
                        <div class="media-body">
                            <h4 class="media-heading">{$prod['product_title']}</h4>
                            <div><i class="fa fa-circle-o"></i> {$prod['product_price']}</div>
                        </div>
                    </div>
                    {/volist}

                    <div class="float-right mt-2">
                        订单总计： <span class="text-danger">￥{$v.payamount}</span>
                    </div>
                </a>
            </div>
            <div class="order-btns text-right">
                {if $v['status'] == 0}
                <a href="javascript:" class="btn btn-secondary btn-cancel" data-id="{$v.order_id}">取消订单</a>
                <a href="javascript:" class="btn btn-danger btn-pay" data-id="{$v.order_id}">重新支付</a>
                {elseif $v['status'] == 3/}
                {elseif $v['status'] > 0/}
                {if $v['isaudit'] == 1}
                <a class="btn btn-secondary btn-confirm" href="javascript:" data-id="{$v.order_id}">确认完成</a>
                {/if}
                {/if}
            </div>
        </div>
        {/volist}
    </div>
    {$page|raw}
</div>
{/block}
{block name="script"}
<script type="text/javascript">
    jQuery(function ($) {
        $('.btn-confirm').click(function () {
            var id = $(this).data('id');
            if (confirm('是否确认订单已收货')) {
                $.ajax({
                    url: "{:aurl('index/member/confirm')}?id=" + id,
                    dataType: 'JSON',
                    success: function (j) {
                        if (j.code == 1) {
                            alert(j.msg);
                            location.reload();
                        } else {
                            alert(j.msg);
                        }
                    }
                })
            }
        });
        $('.btn-pay').click(function (e) {
            var id = $(this).data('id');
            dialog.action(['余额支付', '微信支付'], function (idx) {
                if (idx == 0) {
                    location.href = "{:url('index/order/balancepay',['order_id'=>'__ID__'])}".replace('__ID__', id);
                } else if (idx == 1) {
                    location.href = "{:url('index/order/wechatpay',['order_id'=>'__ID__'])}".replace('__ID__', id);
                }
            });
        });
        $('.btn-cancel').click(function (e) {
            var id = $(this).data('id');
            dialog.confirm('确定取消订单？', function () {
                location.href = "{:aurl('index/member.order/delete',['id'=>'__ID__'])}".replace('__ID__', id);
            });
        });
    })
</script>
{/block}