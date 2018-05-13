<extend name="Public:Base" />

<block name="body">
    <include file="Public/bread" menu="order_index" section="项目" title="订单管理" />

    <div id="page-wrapper">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">订单信息</h3>
            </div>
            <div class="panel-body">
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
                        <td>{$model.status|showstatus}</td>
                    </tr>
                    <tr>
                        <th colspan="4">订单商品</th>
                    </tr>
                    <tr>
                        <td>
                            <volist name="products" id="p">
                            <div class="media">
                                <div class="media-left">
                                    <img class="media-object" src="{$p['product_image']}" alt="{$p['product_title']}">
                                </div>
                                <div class="media-body">
                                    <h4 class="media-heading">{$p['product_title']}</h4>
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
                        <th >支付金额</th>
                        <td colspan="3">
                            {$model.payamount}
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">发货信息</h3>
            </div>
            <div class="panel-body">
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
        </div>
    </div>
</block>