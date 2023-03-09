{extend name="public:base" /}
{block name="body"}
    <div class="container">
        <div class="page-header"><h1>订单详情</h1></div>
        <div class="card">
            <div class="card-header" style="margin-bottom:10px;">{$order.order_no}
                <div class="float-right">{$order.status|order_status|raw}</div>
            </div>
            <div class="card-body">
                {volist name="products" id="prod"}
                    <div class="media">
                        <div class="media-left">
                            <a href="{:url('index/product/view',array('id'=>$prod['product_id']))}">
                                <img class="media-object" width="50" height="50" src="{$prod['product_image']}" alt="...">
                            </a>
                        </div>
                        <div class="media-body">
                            <h4 class="media-heading">{$prod['product_title']}</h4>
                            <div><i class="fa fa-circle-o"></i> {$prod['product_price']}&times;{$prod.count}</div>
                        </div>
                    </div>
                {/volist}
                <div class="float-right mt-2">
                    订单总计： <span class="text-danger">￥{$order.payamount}</span>
                </div>
            </div>
            <div class="card-body">
                <div>{$order.receive_name}&nbsp;/&nbsp;{$order.mobile}</div>
                <div>{$order.province}&nbsp;/&nbsp;{$order.city}&nbsp;/&nbsp;{$order.area}</div>
                <div>{$order.address}</div>
                {if $order['express_no']}
                    {$order['express_code']}：{$order['express_no']}
                {/if}
            </div>
            <div class="card-body">
                <p>下单时间：{$order.create_time|showdate}</p>
            </div>
            <div class="card-footer order-btns text-right">

                {if $order['status'] == 0}
                    <a href="javascript:" class="btn btn-secondary btn-cancel">取消订单</a>
                    <a href="javascript:" class="btn btn-danger btn-pay">重新支付</a>
                    {elseif $order['status'] == 3/}
                    {elseif $order['status'] > 0/}
                    {if $order['isaudit'] == 1}
                        <a class="btn btn-secondary btn-confirm" href="javascript:" data-id="{$order.order_id}">确认完成</a>
                    {/if}
                {/if}
            </div>
        </div>
    </div>
{/block}
{block name="script"}
    <script type="text/javascript">
        jQuery(function($){
            $('.btn-confirm').click(function() {
                var id=$(this).data('id');
                if(confirm('是否确认订单已收货')){
                    $.ajax({
                        url:"{:aurl('index/member/confirm')}?id="+id,
                        dataType:'JSON',
                        success:function(j){
                            if(j.code==1){
                                alert(j.msg);
                                location.reload();
                            }else{
                                alert(j.msg);
                            }
                        }
                    })
                }
            });

            $('.btn-pay').click(function (e) {
                dialog.action(['余额支付','微信支付'],function(idx){
                    if(idx==0){
                        location.href="{:url('index/order/balancepay',['order_id'=>$order['order_id']])}";
                    }else if(idx==1){
                        location.href="{:url('index/order/wechatpay',['order_id'=>$order['order_id']])}";
                    }
                });
            });

            $('.btn-cancel').click(function (e) {
                dialog.confirm('确定取消订单？',function(){
                    location.href="{:aurl('index/member.order/delete',['id'=>$order['order_id']])}";
                });
            });
        })
    </script>
{/block}