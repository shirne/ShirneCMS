<extend name="public:base" />
<block name="body">
    <div class="main-content">
        <div class="page-header"><h1>我的订单</h1></div>
        <ul class="list-group">
            <foreach name="orders" item="v">
            <li class="list-group-item">
                <div style="margin-bottom:10px;">[{$v.apply_id}]  {$v.create_at|showdate}<span class="pull-right"><i class="fa fa-circle-o"></i>{$v.payamount}</span></div>
                <div>
                    <div class="media">
                        <div class="media-left">
                            <a href="{:url('product/index',array('id'=>$v['product']['id']))}">
                                <img class="media-object" width="50" height="50" src="{$v['product']['image']}" alt="...">
                            </a>
                        </div>
                        <div class="media-body">
                            <h4 class="media-heading">{$v['product']['title']}</h4>
                            <div><i class="fa fa-circle-o"></i> {$v['product']['price']}</div>
                        </div>
                    </div>
                </div>
                <div class="order-btns">
                    <if condition="$v['status'] EQ 0">
                        <span class="label label-warning">未支付</span>
                        <elseif condition="$v['status'] EQ 1"/>
                        <if condition="$v['isaudit'] EQ 1">
                            <a class="btn btn-default btn-confirm" href="javascript:" data-id="{$v.apply_id}">确认完成</a>
                            <else/>
                            <span class="label label-warning">待审核</span>
                        </if>
                        <elseif condition="$v['status'] EQ 2"/>
                        <span class="label label-success">已完成</span>
                        <else/>
                        <span class="label label-default">订单已作废</span>
                    </if>
                </div>
            </li>
            </foreach>
        </ul>
    </div>
</block>
<block name="script">
    <script type="text/javascript">
        jQuery(function($){
            $('.btn-confirm').click(function() {
                var id=$(this).data('id');
                if(confirm('是否确认订单已收货')){
                    $.ajax({
                        url:'{:url('member/confirm')}?id='+id,
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