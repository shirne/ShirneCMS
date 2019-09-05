<extend name="public:base" />

<block name="body">

    <include file="public/bread" menu="credit_order_index" title="订单列表" />

    <div id="page-wrapper">

        <div class="row">
            <div class="col-6">
                <div class="btn-group btn-group-sm" role="group" aria-label="Button group with nested dropdown">

                    <div class="btn-group" role="group">
                        <button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            导出订单
                        </button>
                        <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                            <a class="dropdown-item" href="{:url('credit_order/export',['order_ids'=>$orderids])}" target="_blank" >导出本页</a>
                            <a class="dropdown-item" href="{:url('credit_order/export',['status'=>1])}" target="_blank">导出未处理</a>
                            <a class="dropdown-item" href="{:url('credit_order/export',['status'=>$status,'audit'=>$audit,'key'=>base64_encode($key)])}" target="_blank">导出筛选结果</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <form action="{:url('credit_order/index')}" method="post">
                    <div class="form-row">
                        <div class="col-3 form-group">
                            <select name="status" class="form-control form-control-sm">
                                <option value="">全部</option>
                                <option value="1"{$status==='1'?' selected':''}>待发货</option>
                                <option value="2"{$status==='2'?' selected':''}>待收货</option>
                                <option value="3"{$status==='3'?' selected':''}>已完成</option>
                                <option value="-1"{$status==='-1'?' selected':''}>已失效</option>
                            </select>
                        </div>
                        <div class="col-6 form-group input-group input-group-sm">
                            <input type="text" class="form-control" name="key" placeholder="输入关键词搜索">
                            <span class="input-group-append">
                              <button class="btn btn-outline-secondary" type="submit"><i class="ion-md-search"></i></button>
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
                <th>会员</th>
                <th>价格</th>
                <th>时间</th>
                <th width="160">状态</th>
                <th width="280">操作</th>
            </tr>
            </thead>
            <tbody>
            <volist name="lists" id="v">
                <tr>
                    <td>{$v.order_id}</td>
                    <td>
                        <volist name="v['goodss']" id="p">
                        <div class="media">
                            <div class="media-left">
                                <img class="media-object" src="{$p['goods_image']}" alt="{$p['goods_title']}">
                            </div>
                            <div class="media-body">
                                <h4 class="media-heading">{$p['goods_title']}</h4>
                                <div>￥{$p['goods_price']} &times; {$p['count']}件</div>
                            </div>
                        </div>
                        </volist>
                    </td>
                    <td>
                        [{$v.member_id}]{$v['username']}
                    </td>
                    <td>积分：{$v.paycredit}&nbsp;现金：{$v.payamount}</td>
                    <td>{$v.create_time|showdate}</td>
                    <td>
                        {$v.status|order_status|raw}
                    </td>
                    <td>
                        <a class="btn btn-outline-dark btn-sm" href="{:url('credit_order/detail',array('id'=>$v['order_id']))}"><i class="ion-md-create"></i> 详情</a>
                        <a class="btn btn-outline-dark btn-sm btn-status" href="javascript:" data-id="{$v.order_id}"  data-status="2"><i class="ion-md-create"></i> 状态</a>
                        <a class="btn btn-outline-dark btn-sm" href="{:url('credit_order/delete',array('id'=>$v['order_id']))}" style="color:red;" onclick="javascript:return del(this,'您真的确定要删除吗？\n\n删除后将不能恢复!');"><i class="ion-md-trash"></i> 删除</a>
                    </td>
                </tr>
            </volist>
            </tbody>
        </table>
        {$page|raw}
    </div>
</block>
<block name="script">
        <script type="text/plain" id="orderStatus">
            <div class="row" style="margin:0 20%;">
                <div class="col-12 form-group"> 
                    <select class="form-control status-id">
                        <option value="0">待支付</option>
                        <option value="1">已支付</option>
                        <option value="2">已发货</option>
                        <option value="3">已完成</option>
                        <option value="-1">订单作废</option>
                    </select>
                </div>
                <div class="col-12 form-group express_row">
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">快递公司</span></div>
                        <select class="form-control express-code">
                            <option value="">无需快递</option>
                            <foreach name="expresscodes" item="exp" key="k">
                                <option value="{$k}">{$exp}</option>
                            </foreach>
                        </select>
                    </div>
                </div> 
                <div class="col-12 form-group express_row express_no">
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">快递单号</span></div>
                        <input type="text" class="form-control express-no" placeholder="如需物流，请填写单号"/>
                    </div>
                </div> 
            </div>
        </script>
    <script type="text/javascript">
        jQuery(function(){
            
            var tpl=$('#orderStatus').text();
            $('.btn-status').click(function() {
                var id=$(this).data('id');
                var status=$(this).data('status');
                var dlg=new Dialog({
                    onshown:function(body){
                        var select=body.find('select.status-id');
                        var express_code=body.find('.express-code');
                        express_code.change(function(){
                            if($(this).val()){
                                body.find('.express_no').show();
                            }else{
                                body.find('.express_no').hide();
                            }
                        });
                        select.val(status);
                        select.change(function(){
                            if(select.val()=='2'){
                                body.find('.express_row').show();
                                express_code.trigger('change');
                            }else{
                                body.find('.express_row').hide();
                            }
                        }).trigger('change');
                    },
                    onsure:function(body){
                        var data={
                                id:id,
                                status:body.find('select.status-id').val()
                            };
                            if(data.status==2){
                                data['express_code']=body.find('select.express-code').val();
                                data['express_no']=body.find('.express-no').val();
                            }
                        $.ajax({
                            url:'{:url("status")}',
                            type:'POST',
                            data:data,
                            dataType:'JSON',
                            success:function(){
                                dlg.hide();
                                location.reload();
                            }
                        })
                    }
                }).show(tpl,'订单状态');
            });

        });
    </script>

</block>