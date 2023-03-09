{extend name="public:base"/}

{block name="body"}
    <div class="weui-tab">
        <div class="weui-navbar">
            <a href="{:aurl('index/member.order/index',['status'=>0])}" class="weui-navbar__item{$status==0?' active':''}">
                全部订单
            </a>
            <a href="{:aurl('index/member.order/index',['status'=>1])}" class="weui-navbar__item{$status==1?' active':''}">
                待付款
                {if $counts[0] > 0}<span class="counter">{$counts[0]}</span>{/if}
            </a>
            <a href="{:aurl('index/member.order/index',['status'=>2])}" class="weui-navbar__item{$status==2?' active':''}">
                待发货
                {if $counts[1] > 0}<span class="counter">{$counts[1]}</span>{/if}
            </a>
            <a href="{:aurl('index/member.order/index',['status'=>3])}" class="weui-navbar__item{$status==3?' active':''}">
                待收货
                {if $counts[2] > 0}<span class="counter">{$counts[2]}</span>{/if}
            </a>
            <a href="{:aurl('index/member.order/index',['status'=>4])}" class="weui-navbar__item{$status==4?' active':''}">
                待评价
                {if $counts[3] > 0}<span class="counter">{$counts[3]}</span>{/if}
            </a>
        </div>
        <div class="weui-tab__panel">
            {volist name="orders" id="order"}
            <div class="weui-panel weui-panel_access orderlist">
                <div class="weui-panel__hd">
                    <span class="float-right">{$order.status|order_status|raw}</span>
                    {$order.order_no}
                </div>
                <div class="weui-panel__bd">
                    {volist name="order['products']" id="prod"}
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
                            <a href="javascript:" data-payamount="{$order['payamount']}" data-order_id="{$order['order_id']}" class="weui-flex__item danger-btn paybtn">
                                重新支付
                            </a>
                            {elseif condition="$order['status'] > 1" /}
                            <a href="{:aurl('index/member.order/detail',['id'=>$order['order_id']])}" class="weui-flex__item">
                                查看物流
                            </a>
                        {/if}
                        <a href="{:aurl('index/member.order/detail',['id'=>$order['order_id']])}" class="weui-flex__item primary">
                            <div class="weui-cell__bd">订单详情</div>
                        </a>
                    </div>

                </div>
            </div>
            {/volist}
            {$page|raw}
        </div>
    </div>
{/block}
{block name="script"}
    <script type="text/javascript">
        jQuery(function ($) {
            $('.paybtn').click(function() {
                var order_id=$(this).data('order_id');
                var payamount=$(this).data('payamount')*100;
                weui.actionSheet([
                    {
                        label: '余额支付',
                        onClick: function () {
                            var balance=parseInt('{$user.money}');
                            if(balance<payamount){
                                weui.alert('您的余额不足以支付');
                            }else{
                                location.href="{:url('index/order/balancepay',['order_id'=>'__order_id__'])}".replace('__order_id__',order_id);
                            }
                        }
                    }, {
                        label: '微信支付',
                        onClick: function () {
                            location.href="{:url('index/order/wechatpay',['order_id'=>'__order_id__'])}".replace('__order_id__',order_id);
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
                    onClose: function(){

                    }
                });
            })
        })
    </script>
{/block}