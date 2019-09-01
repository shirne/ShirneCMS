<extend name="public:base" />

<block name="body">
    <include file="public/bread" menu="order_index" section="项目" title="订单管理" />

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
                        <th >支付金额</th>
                        <td colspan="3">
                            ￥{$model.payamount}
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <if condition="$model['status'] GT 1">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">发货信息</h3>
            </div>
            <div class="panel-body">
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
        </div>
        </if>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">收货信息</h3>
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