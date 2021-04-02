{extend name="public:base" /}

{block name="body"}

    {include file="public/bread" menu="credit_shop_order_index" title="订单列表" /}

    <div id="page-wrapper">

        <div class="row">
            <div class="col-6">
                <div class="btn-group btn-group-sm" role="group" aria-label="Button group with nested dropdown">

                    <div class="btn-group btn-group-sm" role="group">
                        <button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            导出订单
                        </button>
                        <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                            <a class="dropdown-item" href="{:url('credit_shop.order/export',['order_ids'=>$orderids])}" target="_blank" >导出本页</a>
                            <a class="dropdown-item" href="{:url('credit_shop.order/export',['status'=>1])}" target="_blank">导出未处理</a>
                            <a class="dropdown-item" href="{:url('credit_shop.order/export',['status'=>$status,'audit'=>$audit,'key'=>base64_encode($key)])}" target="_blank">导出筛选结果</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <form action="{:url('credit_shop.order/index')}" method="post">
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
                            <input type="text" class="form-control" name="key" value="{$keyword}" placeholder="输入关键词搜索">
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
                <th width="160">价格</th>
                <th width="100">时间</th>
                <th width="80">状态</th>
                <th width="120">操作</th>
            </tr>
            </thead>
            <tbody>
            {volist name="lists" id="v"}
                <tr>
                    <td>{$v.order_id}</td>
                    <td>
                        {volist name="v['goodss']" id="p"}
                        <div class="media">
                            <div class="media-left">
                                <img class="media-object" src="{$p['goods_image']}" alt="{$p['goods_title']}">
                            </div>
                            <div class="media-body">
                                <h4 class="media-heading">{$p['goods_title']}</h4>
                                <div>￥{$p['goods_price']} &times; {$p['count']}件</div>
                            </div>
                        </div>
                        {/volist}
                    </td>
                    <td>
                        [{$v.member_id}]{$v['username']}
                    </td>
                    <td>积分：{$v.paycredit}<br />
                        现金：{$v.payamount}{if condition="$v['status'] EQ 0"}<a href="javascript:" class="reprice" data-id="{$v.order_id}" data-price="{$v['payamount']}" title="改价"><i class="ion-md-create"></i> </a> {/if}
                    </td>
                    <td>{$v.create_time|showdate}</td>
                    <td>
                        {$v.status|order_status|raw}
                    </td>
                    <td class="operations">
                        <a class="btn btn-outline-primary" title="详情" href="{:url('credit_shop.order/detail',array('id'=>$v['order_id']))}"><i class="ion-md-document"></i> </a>
                        
                        {if condition="$v['status'] EQ 0"}
                            <a class="btn btn-outline-danger btn-status" title="取消订单" href="javascript:" data-id="{$v.order_id}"  data-status="-1" ><i class="ion-md-close-circle-outline"></i> </a>
                            <a class="btn btn-outline-warning btn-status" title="设置支付状态" href="javascript:" data-id="{$v.order_id}"  data-status="1" ><i class="ion-md-wallet"></i> </a>
                        {elseif condition="$v['status'] EQ 1" /}
                            <a class="btn btn-outline-info btn-status" title="发货" href="javascript:" data-id="{$v.order_id}"  data-status="2" data-express="{$v.express_code}/{$v.express_no}"><i class="ion-md-train"></i> </a>
                        {elseif condition="$v['status'] EQ 2" /}
                            <a class="btn btn-outline-secondary btn-status" title="修改发货信息" href="javascript:" data-id="{$v.order_id}"  data-status="2" data-express="{$v.express_code}/{$v.express_no}"><i class="ion-md-subway"></i> </a>
                            <a class="btn btn-outline-success btn-status" title="收货" href="javascript:" data-id="{$v.order_id}"  data-status="3" ><i class="ion-md-exit"></i> </a>
                        {elseif condition="$v['status'] EQ 3" /}
                            <a class="btn btn-outline-success btn-status" title="完成" href="javascript:" data-id="{$v.order_id}"  data-status="4" ><i class="ion-md-checkbox-outline"></i> </a>
                        {/if}
                        <a class="btn btn-outline-danger link-confirm" title="删除" data-confirm="您真的确定要删除吗？\n删除后将不能恢复!" href="{:url('credit_shop.order/delete',array('id'=>$v['order_id']))}"><i class="ion-md-trash"></i> </a>
                    </td>
                </tr>
            {/volist}
            </tbody>
        </table>
        {$page|raw}
    </div>
{/block}
{block name="script"}
        {include file="$statusTpl" /}
    <script type="text/javascript">
        jQuery(function(){
            
            $('.reprice').click(function (e) {
                var id=$(this).data('id');
                var orig_price=$(this).data('price')
                var dlg=dialog.prompt('当前价格：'+orig_price,function (input) {
                    $.ajax({
                        url:'{:url("reprice")}',
                        type:'POST',
                        data:{
                            id:id,
                            price:input,
                        },
                        dataType:'JSON',
                        success:function(json){
                            if(json.code==1) {
                                dlg.hide();
                                location.reload();
                            }else{
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