{extend name="public:base"/}

{block name="body"}
<div class="page__hd">
    <h1 class="page__title">订单详情</h1>
</div>
<div class="page__bd">
    <div class="weui-panel weui-panel_access orderlist">
        <div class="weui-panel__hd">
            <span class="float-right">{$order.status|order_status|raw}</span>
            {$order.order_no}
        </div>
        <div class="weui-panel__bd">
            {volist name="products" id="prod"}
            <a href="javascript:void(0);" class="weui-media-box weui-media-box_appmsg">
                <div class="weui-media-box__hd">
                    <img class="weui-media-box__thumb" src="{$prod.product_image}" alt="">
                </div>
                <div class="weui-media-box__bd">
                    <h4 class="weui-media-box__title">{$prod.product_title}</h4>
                    <p class="weui-media-box__desc">{$prod.options}</p>
                </div>
                <div class="weui-media-box__ft">
                    {$prod.product_price}<br />
                    &times;
                    {$prod.count}
                </div>
            </a>
            {/volist}
        </div>
        <div class="weui-panel__ft">
            <div class="weui-flex">
                {if $order['status'] == 0}
                <a href="javascript:" data-payamount="{$order['payamount']}" class="weui-flex__item danger-btn paybtn">
                    重新支付
                </a>
                <a href="{:aurl('index/member.order/delete',['id'=>$order['order_id']])}"
                    class="weui-flex__item danger-btn delete-btn">
                    <div class="weui-cell__bd">删除订单</div>
                </a>
                {elseif condition="$order['status'] > 1" /}
                <a href="{:aurl('index/member.order/detail',['id'=>$order['order_id']])}" class="weui-flex__item">
                    查看物流
                </a>

                {if condition="$order['status'] > 2" }
                <a href="{:aurl('index/member.order/delete',['id'=>$order['order_id']])}"
                    class="weui-flex__item danger-btn delete-btn">
                    <div class="weui-cell__bd">删除订单</div>
                </a>
                {else/}
                <a href="{:aurl('index/member.order/confirm',['id'=>$order['order_id']])}"
                    class="weui-flex__item primary confirm-btn">
                    <div class="weui-cell__bd">确认收货</div>
                </a>
                {/if}
                {/if}
            </div>

        </div>
    </div>
</div>
{/block}
{block name="script"}
<script type="text/javascript">
    jQuery(function ($) {
        $('.paybtn').click(function () {
            var order_id = $(this).data('order_id');
            var payamount = $(this).data('payamount') * 100;
            weui.actionSheet([
                {
                    label: '余额支付',
                    onClick: function () {
                        var balance = parseInt('{$user.money}');
                        if (balance < payamount) {
                            weui.alert('您的余额不足以支付');
                        } else {
                            location.href = '{:url('index / order / balancepay',['order_id'=>$order['order_id']])}';
                        }
                    }
                }, {
                    label: '微信支付',
                    onClick: function () {
                        location.href = '{:url('index / order / wechatpay',['order_id'=>$order['order_id']])}';
                    }
                }
            ], [
                {
                    label: '取消',
                    onClick: function () {

                    }
                }
            ], {
                className: 'custom-classname',
                onClose: function () {

                }
            });
        });
        $('.delete-btn').click(function (e) {
            e.preventDefault();
            e.stopPropagation();
            var url = $(this).attr('href');
            weui.confirm('删除订单后不可恢复，确认删除？', function () {
                var loading = weui.loading('正在提交');
                $.ajax({
                    url: url,
                    dataType: 'JSON',
                    success: function (json) {
                        loading.hide();
                        if (json.code == '1') {
                            weui.alert(json.msg, function () {
                                location.href = "{:aurl('index/member.order/index')}";
                            });
                        } else {
                            weui.alert(json.msg);
                        }
                    }
                })
            })
        });
        $('.confirm-btn').click(function (e) {
            e.preventDefault();
            e.stopPropagation();
            var url = $(this).attr('href');
            weui.confirm('是否确认已收到货并且外观完整？', function () {
                var loading = weui.loading('正在提交');
                $.ajax({
                    url: url,
                    dataType: 'JSON',
                    success: function (json) {
                        loading.hide();
                        if (json.code == '1') {
                            weui.alert(json.msg, function () {
                                location.reload();
                            });
                        } else {
                            weui.alert(json.msg);
                        }
                    }
                })
            })
        })
    })
</script>
{/block}