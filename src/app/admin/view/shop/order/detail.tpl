{extend name="public:base" /}

{block name="body"}
    {include  file="public/bread" menu="shop_order_index" section="项目" title="订单管理"  /}

    <div id="page-wrapper" class="page-form">
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">订单信息</h3>
            </div>
            <table class="table">
                <tbody>
                <tr>
                    <td>订单号</td>
                    <td>{$model.order_no}</td>
                    <td>下单会员</td>
                    <td>[{$member.id}]{$member.username}</td>
                </tr>
                <tr>
                    <td>下单日期</td>
                    <td>{$model.create_time|showdate}</td>
                    <td>订单状态</td>
                    <td>{$model.status|order_status|raw}</td>
                </tr>
                <tr>
                    <th colspan="4">订单商品</th>
                </tr>
                <tr>
                    <td colspan="4">
                        <volist name="products" id="p">
                        <div class="media p-2 bg-light">
                            <div class="media-left">
                                <img class="media-object" src="{$p['product_image']}" alt="{$p['product_title']}">
                            </div>
                            <div class="media-body">
                                <h4 class="media-heading">{$p['product_title']}</h4>
                                <div class="text-muted">
                                    <if condition="!empty($p['sku_specs'])">
                                        <foreach name="$p['sku_specs']" key="spec" item="value">
                                            <span>{$spec}:{$value}&nbsp;</span>
                                        </foreach>
                                        <else/>
                                        默认规格
                                    </if>
                                </div>
                                <div>￥{$p['product_price']} &times; {$p['count']}件</div>
                            </div>
                        </div>
                        </volist>
                    </td>
                </tr>
                <if condition="$model['remark']">
                <tr>
                    <th colspan="4">订单备注</th>
                </tr>
                <tr>
                    <td>
                        {$model.remark}
                    </td>
                </tr>
                </if>
                <tr>
                    <th>商品金额</th>
                    <td>{$model.product_amount}</td>
                    <th>邮费</th>
                    <td>{$model.postage}</td>
                </tr>
                <tr>
                    <th >支付金额</th>
                    <td colspan="3">
                        ￥{$model.payamount}
                        <if condition="!empty($model['pay_type'])">
                            <if condition="$model['pay_type'] EQ 'offline'">
                                <span class="badge badge-warning">线下支付</span>
                                <elseif condition="$model['pay_type'] EQ 'balance'"/>
                                <span class="badge badge-primary">余额支付</span>
                                <elseif condition="$model['pay_type'] EQ 'wechat'"/>
                                <span class="badge badge-success">微信支付</span>
                                <elseif condition="$model['pay_type'] EQ 'alipay'"/>
                                <span class="badge badge-info">支付宝</span>
                                <else/>
                                <span class="badge badge-secondary">{$model['pay_type']}</span>
                            </if>
                        </if>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <if condition="!empty($payorders)">
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">支付信息</h3>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>支付单号</th>
                            <th>发起时间</th>
                            <th>支付类型</th>
                            <th>支付状态</th>
                            <th>交易号</th>
                            <th>是否有退款</th>
                        </tr>
                    </thead>
                    <tbody>
                    <volist name="payorders" id="po">
                    <tr>
                        <td>订单号</td>
                        <td>{$model.order_no}</td>
                        <td>下单会员</td>
                        <td>[{$member.id}]{$member.username}</td>
                    </tr>
                    <tr>
                        <td>下单日期</td>
                        <td>{$model.create_time|showdate}</td>
                        <td>订单状态</td>
                        <td>{$model.status|order_status|raw}</td>
                    </tr>
                    <tr>
                        <th colspan="4">订单商品</th>
                    </tr>
                    <tr>
                        <td colspan="4">
                            {volist name="products" id="p"}
                            <div class="media p-2 bg-light">
                                <div class="media-left">
                                    <img class="media-object" src="{$p['product_image']}" alt="{$p['product_title']}">
                                </div>
                                <div class="media-body">
                                    <h4 class="media-heading">{$p['product_title']}</h4>
                                    <div class="text-muted">
                                        {if !empty($p['sku_specs'])}
                                            {foreach name="$p['sku_specs']" key="spec" item="value"}
                                                <span>{$spec}:{$value}&nbsp;</span>
                                            {/foreach}
                                            {else/}
                                            默认规格
                                        {/if}
                                    </div>
                                    <div>￥{$p['product_price']} &times; {$p['count']}件</div>
                                </div>
                            </div>
                            {/volist}
                        </td>
                    </tr>
                    {if $model['remark']}
                    <tr>
                        <th colspan="4">订单备注</th>
                    </tr>
                    <tr>
                        <td>
                            <if condition="$po['status'] == 1">
                                <span class="badge badge-success">已支付</span><br />
                                <span class="badge badge-secondary">{$po.pay_time|showdate}</span>
                            <elseif condition="$po['status'] LT 0"/>
                                <span class="badge badge-secondary">已失效</span>
                                <else/>
                                <span class="badge badge-warning has-tooltip paystatus" title="查询支付状态" data-id="{$po.id}" >未支付</span>
                            </if>

                        </td>
                    </tr>
                    {/if}
                    <tr>
                        <th>商品金额</th>
                        <td>{$model.product_amount}</td>
                        <th>邮费</th>
                        <td>{$model.postage}</td>
                    </tr>
                    <tr>
                        <th >支付金额</th>
                        <td colspan="3">
                            ￥{$model.payamount}
                            {if !empty($model['pay_type'])}
                                {if $model['pay_type'] EQ 'offline'}
                                    <span class="badge badge-warning">线下支付</span>
                                    {elseif $model['pay_type'] EQ 'balance'/}
                                    <span class="badge badge-primary">余额支付</span>
                                    {elseif $model['pay_type'] EQ 'wechat'/}
                                    <span class="badge badge-success">微信支付</span>
                                    {elseif $model['pay_type'] EQ 'alipay'/}
                                    <span class="badge badge-info">支付宝</span>
                                    {else/}
                                    <span class="badge badge-secondary">{$model['pay_type']}</span>
                                {/if}
                            {/if}
                        </td>
                    </tr>
                    </volist>
                    </tbody>
                </table>
            </div>
        </div>
        {if !empty($payorders)}
            <div class="card">
                <div class="card-header">
                    <h3 class="panel-title">支付信息</h3>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>支付单号</th>
                                <th>发起时间</th>
                                <th>支付类型</th>
                                <th>支付状态</th>
                                <th>交易号</th>
                                <th>是否有退款</th>
                            </tr>
                        </thead>
                        <tbody>
                        {volist name="payorders" id="po"}
                        <tr>
                            <td>{$po.order_no}</td>
                            <td>{$po.create_time|showdate}</td>
                            <td>
                                {if $po['pay_type'] == 'wechat'}
                                    <span class="badge badge-success has-tooltip" title="{$po.trade_type}">微信支付</span>
                                {elseif $po['pay_type'] == 'alipay' /}
                                    <span class="badge badge-info">支付宝</span>
                                {else/}
                                    <span class="badge badge-secondary">{$po.pay_type}</span>
                                {/if}
                            </td>
                            <td>
                                {if $po['status'] == 1}
                                    <span class="badge badge-success">已支付</span><br />
                                    <span class="badge badge-secondary">{$po.pay_time|showdate}</span>
                                {elseif $po['status'] LT 0/}
                                    <span class="badge badge-secondary">已失效</span>
                                    {else/}
                                    <span class="badge badge-warning has-tooltip paystatus" title="查询支付状态" data-id="{$po.id}" >未支付</span>
                                {/if}

                            </td>
                            <td>{$po.pay_bill}</td>
                            <td>
                                {if $po['is_refund'] == 1}
                                    <span class="badge badge-warning">有退款</span>
                                    {$po.refund_fee}
                                {else/}
                                    <span class="badge badge-secondary">无退款</span>
                                {/if}
                            </td>
                        </tr>
                        {/volist}
                        </tbody>
                    </table>
                </div>
            </div>
        {/if}
        {if $model['status'] GT 1}
        <div class="card">
            <div class="card-header">
                <h3 class="panel-title">发货信息</h3>
            </div>
            <div class="card-body">
                <table class="table">
                    <tbody>
                    <tr>
                        <td>快递公司</td>
                        <td>{if !empty($model['express_code'])}[{$model.express_code}]{php}$expresses=config('express');{/php}{$expresses[$model['express_code']]}{else/}无需物流{/if}</td>
                        <td>快递单号</td>
                        <td>{$model.express_no}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <table class="table">
                <tbody>
                <tr>
                    <td>快递公司</td>
                    <td><if condition="!empty($model['express_code'])">[{$model.express_code}]<php>$expresses=config('express.');</php>{$expresses[$model['express_code']]}<else/>无需物流</if></td>
                    <td>快递单号</td>
                    <td>{$model.express_no}</td>
                </tr>
                </tbody>
            </table>
        </div>
        {/if}
        <div class="card">
            <div class="card-header">
                <h3 class="panel-title">收货信息</h3>
            </div>
            <table class="table">
                <tbody>
                <tr>
                    <td>收货人</td>
                    <td>{$model.recive_name}</td>
                    <td>电话</td>
                    <td>{$model.mobile}</td>
                </tr>
                <tr>
                    <td>地址</td>
                    <td colspan="3">{$model.province} {$model.city} {$model.area} {$model.address}</td>
                </tr>
                </tbody>
            </table>
        </div>
        <if condition="!empty($refunds)">
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">退款申请</h3>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>类型</th>
                            <th>原因</th>
                            <th>联系电话</th>
                            <th>提交日期</th>
                            <th>目前状态</th>
                            <th>处理</th>
                        </tr>
                    </thead>
                    <tbody>
                    <volist name="refunds" id="po">
                    <tr>
                        <td>{$po.type}</td>
                        <td>{$po.reason}</td>
                        <td>{$po.mobile}</td>
                        <td>{$po.create_time|showdate}</td>
                        <td>
                            <if condition="$po['status'] == 1">
                                <span class="badge badge-success">已处理</span><br />
                                <span class="badge badge-secondary">{$po.update_time|showdate}</span>
                            <elseif condition="$po['status'] LT 0"/>
                                <span class="badge badge-secondary">已拒绝</span>
                                <else/>
                                <span class="badge badge-warning"  >待处理</span>
                            </if>

                        </td>
                        <td>
                            <if condition="$po['status'] == 0">
                                <a href="javascript:" class="btn btn-sm btn-outline-danger btn-refund-cancel" data-id="{$po.id}">拒绝</a>
                                <a href="javascript:" class="btn btn-sm btn-outline-primary btn-refund-allow" data-id="{$po.id}">通过</a>
                            </if>
                        </td>
                    </tr>
                    </volist>
                    </tbody>
                </table>
            </div>
        </div>
        {if !empty($refunds)}
            <div class="card">
                <div class="card-header">
                    <h3 class="panel-title">退款申请</h3>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>类型</th>
                                <th>原因</th>
                                <th>联系电话</th>
                                <th>提交日期</th>
                                <th>目前状态</th>
                                <th>处理</th>
                            </tr>
                        </thead>
                        <tbody>
                        {volist name="refunds" id="po"}
                        <tr>
                            <td>{$po.type}</td>
                            <td>{$po.reason}</td>
                            <td>{$po.mobile}</td>
                            <td>{$po.create_time|showdate}</td>
                            <td>
                                {if $po['status'] == 1}
                                    <span class="badge badge-success">已处理</span><br />
                                    <span class="badge badge-secondary">{$po.update_time|showdate}</span>
                                {elseif $po['status'] < 0/}
                                    <span class="badge badge-secondary">已拒绝</span>
                                    {else/}
                                    <span class="badge badge-warning"  >待处理</span>
                                {/if}

                            </td>
                            <td>
                                {if $po['status'] == 0}
                                    <a href="javascript:" class="btn btn-sm btn-outline-danger btn-refund-cancel" data-id="{$po.id}">拒绝</a>
                                    <a href="javascript:" class="btn btn-sm btn-outline-primary btn-refund-allow" data-id="{$po.id}">通过</a>
                                {/if}
                            </td>
                        </tr>
                        {/volist}
                        </tbody>
                    </table>
                </div>
            </div>
        {/if}
        {if $model['status'] > -1 AND $model['status'] < 4}
            <div class="form-group submit-btn">
                {if $model['status'] EQ 0}
                    <a class="btn btn-outline-danger btn-status" title="取消订单" data-id="{$model.order_id}" href="javascript:"  data-status="-1" ><i class="ion-md-close-circle-outline"></i> 取消订单</a>
                    <a class="btn btn-outline-warning btn-status" title="设置支付状态" data-id="{$model.order_id}" href="javascript:" data-status="1" ><i class="ion-md-wallet"></i> 设置支付状态</a>
                {elseif $model['status'] EQ 1 /}
                    <a class="btn btn-outline-info btn-status" title="发货" href="javascript:" data-id="{$model.order_id}" data-status="2" data-express="{$model.express_code}/{$model.express_no}"><i class="ion-md-train"></i> 发货</a>
                {elseif $model['status'] EQ 2 /}
                    <a class="btn btn-outline-secondary btn-status" title="修改发货信息" href="javascript:" data-id="{$model.order_id}" data-status="2" data-express="{$model.express_code}/{$model.express_no}"><i class="ion-md-subway"></i> 修改发货信息</a>
                    <a class="btn btn-outline-success btn-status" title="收货" href="javascript:" data-id="{$model.order_id}" data-status="3" ><i class="ion-md-exit"></i> 收货</a>
                {elseif $model['status'] EQ 3 /}
                    <a class="btn btn-outline-success btn-status" title="完成" href="javascript:" data-id="{$model.order_id}"  data-status="4" ><i class="ion-md-checkbox-outline"></i> 完成订单</a>
                {/if}
            </div>
        {/if}
    </div>
{/block}
{block name="script"}
        {include  file="shop/order/_status_tpl"  /}
    <script type="text/javascript">
        jQuery(function($){
            

            $('.paystatus').click(function(e){
                var id=$(this).data('id');
                var loading=dialog.loading();
                $.ajax({
                    url:"{:url('payquery')}",
                    dataType:'json',
                    data:{
                        payid:id
                    },
                    success:function(json){
                        loading.close();
                        if(json.code==1){
                            dialog.success(json.msg)
                        }else{
                            dialog.error(json.msg)
                        }
                        setTimeout(function(){
                            location.reload()
                        },800)
                    }
                })
            });

            $('.btn-refund-cancel').click(function(){
                var id = $(this).data('id');
                dialog.prompt('推荐与客户电话沟通解决问题并填写拒绝原因',function(text){
                    $.ajax({
                        url:"{:url('refundcancel',['id'=>'__ID__'])}".replace('__ID__',id),
                        data:{reason:text},
                        dataType:'json',
                        type:'POST',
                        success:function(json){
                            if(json.code == 1){
                                dialog.alert(json.msg,function(){
                                    location.reload()
                                });
                            }else{
                                dialog.error(json.msg)
                            }
                        }
                    })
                });
            })
            $('.btn-refund-allow').click(function(){
                var id = $(this).data('id');
                dialog.prompt('推荐与客户电话沟通解决问题并填写拒绝原因',function(text){
                    $.ajax({
                        url:"{:url('refundallow',['id'=>'__ID__'])}".replace('__ID__',id),
                        dataType:'json',
                        type:'POST',
                        success:function(json){
                            if(json.code == 1){
                                dialog.alert(json.msg,function(){
                                    location.reload()
                                });
                            }else{
                                dialog.error(json.msg)
                            }
                        }
                    })
                });
            })
        })
    </script>
{/block}