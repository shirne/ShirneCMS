<extend name="public:base" />
<block name="body">
    <div class="container">
        <div class="page-header"><h1>我的订单</h1></div>
        <ul class="list-group">
            <php>$empty='<span class="col-12 empty">您还没有下单哦</span>';</php>
            <foreach name="orders" empty="$empty" item="v">
            <li class="list-group-item">
                <div style="margin-bottom:10px;">[{$v.order_no}]  {$v.create_at|showdate}<span class="pull-right"><i class="fa fa-circle-o"></i>{$v.payamount}</span></div>
                <div>
                    <volist name="v.products" id="prod">
                    <div class="media">
                        <div class="media-left">
                            <a href="{:url('index/product/view',array('id'=>$prod['product_id']))}">
                                <img class="media-object" width="50" height="50" src="{$prod['product_image']}" alt="...">
                            </a>
                        </div>
                        <div class="media-body">
                            <h4 class="media-heading">{$prod['product_title']}</h4>
                            <div><i class="fa fa-circle-o"></i> {$prod['product_price']}</div>
                        </div>
                    </div>
                    </volist>
                </div>
                <div class="order-btns text-right">
                    <if condition="$v['status'] EQ 0">
                        <span class="badge badge-warning">未支付</span>
                        <elseif condition="$v['status'] EQ 1"/>
                        <if condition="$v['isaudit'] EQ 1">
                            <a class="btn btn-default btn-confirm" href="javascript:" data-id="{$v.apply_id}">确认完成</a>
                            <else/>
                            <span class="badge badge-warning">待审核</span>
                        </if>
                        <elseif condition="$v['status'] EQ 2"/>
                        <span class="badge badge-success">已完成</span>
                        <else/>
                        <span class="badge badge-default">订单已作废</span>
                    </if>
                </div>
            </li>
            </foreach>
        </ul>
        {$page|raw}
    </div>
</block>
<block name="script">
    <script type="text/javascript">
        jQuery(function($){
            $('.btn-confirm').click(function() {
                var id=$(this).data('id');
                if(confirm('是否确认订单已收货')){
                    $.ajax({
                        url:"{:url('index/member/confirm')}?id="+id,
                        dataType:'JSON',
                        success:function(j){
                            if(j.status==1){
                                alert(j.info);
                                location.reload();
                            }else{
                                alert(j.info);
                            }
                        }
                    })
                }
            })
        })
    </script>
</block>