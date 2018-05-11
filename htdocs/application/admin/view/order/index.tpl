<extend name="public:base" />

<block name="body">

    <include file="public/bread" menu="order_index" title="订单列表" />

    <div id="page-wrapper">

        <div class="row">
            <div class="col-6">
                &nbsp;
            </div>
            <div class="col-6">
                <form action="{:url('links/index')}" method="post">
                    <div class="form-group input-group input-group-sm">
                        <input type="text" class="form-control" name="key" placeholder="输入关键词搜索">
                        <span class="input-group-append">
                          <button class="btn btn-outline-secondary" type="submit"><i class="ion-md-search"></i></button>
                        </span>
                    </div>
                </form>
            </div>
        </div>
        <table class="table table-hover table-striped">
            <thead>
            <tr>
                <th width="50">编号</th>
                <th>商品</th>
                <th>会员</th>
                <th>价格</th>
                <th>时间</th>
                <th width="160">状态</th>
                <th width="280">操作</th>
            </tr>
            </thead>
            <tbody>
            <foreach name="model" item="v">
                <tr>
                    <td>{$v.apply_id}</td>
                    <td>
                        <div class="media">
                            <div class="media-left">
                                <img class="media-object" src="{$v['product']['image']}" alt="{$v['product']['title']}">
                            </div>
                            <div class="media-body">
                                <h4 class="media-heading">{$v['product']['title']}</h4>
                                <div>￥{$v['product']['price']}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        [{$v.member_id}]{$v['member']['username']}
                    </td>
                    <td>{$v.payamount}</td>
                    <td>{$v.create_at|showdate}</td>
                    <td>
                        {$v.status|showstatus}
                        <if condition="$v['isaudit'] EQ 1">
                            <span class="label label-default">已审核</span>
                            <else/>
                            <span class="label label-warning">待审核</span>
                        </if>
                    </td>
                    <td>
                        <a class="btn btn-default btn-sm" href="{:url('order/detail',array('id'=>$v['apply_id']))}"><i class="fa fa-edit"></i> 详情</a>
                        <a class="btn btn-default btn-sm btn-status" href="javascript:" data-id="{$v.apply_id}"  data-status="2"><i class="fa fa-edit"></i> 状态</a>
                        <if condition="$v['rebated'] NEQ 1"><a class="btn btn-default btn-sm btn-audit" href="javascript:" data-id="{$v.apply_id}"  data-status="1"><i class="fa fa-lock"></i> 审核</a></if>
                        <!--a class="btn btn-default btn-sm" href="{:url('order/exprerss',array('id'=>$v['id']))}"><i class="fa fa-edit"></i> 发货</a-->
                        <a class="btn btn-default btn-sm" href="{:url('order/delete',array('id'=>$v['apply_id']))}" style="color:red;" onclick="javascript:return del('您真的确定要删除吗？\n\n删除后将不能恢复!');"><i class="fa fa-trash"></i> 删除</a>
                    </td>
                </tr>
            </foreach>
            </tbody>
        </table>
        {$page}
    </div>
</block>
<block name="script">
    <script type="text/javascript">
        jQuery(function(){
            var tpl='<div class="row" style="margin:0 20%;">' +
                '<div class="form-group"> <select class="form-control status-id"><option value="0">待支付</option><option value="1">已支付</option><option value="2">已完成</option><option value="-1">订单作废</option></select></div>' +
                '<div class="form-group"><div class="input-group"><span class="input-group-addon">快递单号</span> <input type="text" class="form-control" placeholder="如已发货，请填写单号"/> </div></div> '+
                '</div>';
            $('.btn-status').click(function() {
                var id=$(this).data('id');
                var status=$(this).data('status');
                var dlg=new Dialog({
                    onshown:function(body){
                        var select=body.find('select.status-id');
                        select.val(status);
                    },
                    onsure:function(body){
                        $.ajax({
                            url:'{:url("status")}',
                            type:'POST',
                            data:{
                                id:id,
                                status:body.find('select.status-id').val(),
                                express_code:body.find('input').val()
                            },
                            dataType:'JSON',
                            success:function(){
                                dlg.hide();
                                location.reload();
                            }
                        })
                    }
                }).show(tpl,'订单状态');
            });

            var tpl2='<div class="row" style="margin:0 20%;">' +
                '<div class="form-group"> <select class="form-control status-id"><option value="0">待审核</option><option value="1">已审核</option></select></div>' +
                '</div>';
            $('.btn-audit').click(function() {
                var id=$(this).data('id');
                var status=$(this).data('status');
                var dlg=new Dialog({
                    onshown:function(body){
                        var select=body.find('select.status-id');
                        select.val(status);
                    },
                    onsure:function(body){
                        $.ajax({
                            url:'{:url("audit")}',
                            type:'POST',
                            data:{
                                id:id,
                                status:body.find('select.status-id').val(),
                            },
                            dataType:'JSON',
                            success:function(){
                                dlg.hide();
                                location.reload();
                            }
                        })
                    }
                }).show(tpl2,'订单审核');
            });
        });
    </script>

</block>