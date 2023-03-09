{extend name="public:base" /}
{block name="header"}
    <style type="text/css">
        html{overflow-y:scroll;}
    </style>
{/block}
{block name="body"}

    {include file="public/bread" menu="shop_order_statics_index" title="订单统计" /}

    <div id="page-wrapper">
        <div class="list-header">
            <form class="noajax" action="{:url('shop.orderStatics/region')}" method="post">
                <div class="form-row">
                    <div class="col-1">
                        <a href="{:url('shop.orderStatics/index')}" class="btn btn-sm btn-primary">周期统计</a>
                    </div>
                    <div class="form-group col input-group input-group-sm date-range">
                        <div class="input-group-prepend">
                            <span class="input-group-text">下单时间</span>
                        </div>
                        <input type="text" class="form-control fromdate" name="start_date" placeholder="选择开始日期" value="{$start_date}">
                        <div class="input-group-middle"><span class="input-group-text">-</span></div>
                        <input type="text" class="form-control todate" name="end_date" placeholder="选择结束日期" value="{$end_date}">
                    </div>
                    <div class="form-group col">
                        <div class="btn-group btn-group-sm btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-outline-secondary {$static_type=='city'?'active':''}">
                                <input type="radio" name="type" id="option1" value="city" autocomplete="off" checked> 按城市
                            </label>
                            <label class="btn btn-outline-secondary {$static_type=='province'?'active':''}">
                                <input type="radio" name="type" id="option2" value="province" autocomplete="off"> 按省份
                            </label>
                        </div>
                        <input type="submit" class="btn btn-primary btn-sm btn-submit ml-2" value="确定"/>
                    </div>
                </div>
            </form>
        </div>
        <div class="row">
        <div class="chart-box col col-lg-8">
        <canvas id="myChart" width="800" height="400"></canvas>
        </div>
        <div class="table-box col col-lg-4">
            <table class="table table-hover table-striped">
                <thead>
                <tr>
                    <th width="50">地区</th>
                    <th>单量</th>
                    <th>金额</th>
                    <th>成本</th>
                    <th>返奖额</th>
                </tr>
                </thead>
                <tbody>
                    {foreach $statics $item}
                        <tr>
                            <th>{$item['region']}</th>
                            <td>{$item['order_count']}</td>
                            <td>{$item['order_amount']}</td>
                            <td>{$item['total_cost_amount']}</td>
                            <td>{$item['order_rebate']}</td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
        </div>
    </div>

{/block}
{block name="script"}
    <script type="text/javascript" src="__STATIC__/chart/Chart.bundle.min.js"></script>
    <script type="text/javascript">
        var ctx = document.getElementById("myChart");
        var bgColors=[
            'rgba(255, 99, 132, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(255, 206, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(255, 159, 64, 0.2)'
        ];
        var bdColors=[
            'rgba(255,99,132,1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)'
        ];
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: JSON.parse('{:json_encode(array_column($statics,"region"))}'),
                datasets: [{
                    label: '会员下单量',
                    data: JSON.parse('{:json_encode(array_column($statics,"order_count"))}'),
                    backgroundColor: bgColors[0],
                    borderColor: bdColors[0],
                    borderWidth: 1
                },{
                    label: '订单总金额',
                    data: JSON.parse('{:json_encode(array_column($statics,"order_amount"))}'),
                    backgroundColor: bgColors[1],
                    borderColor: bdColors[1],
                    borderWidth: 1
                },{
                    label: '订单总成本',
                    data: JSON.parse('{:json_encode(array_column($statics,"total_cost_amount"))}'),
                    backgroundColor: bgColors[2],
                    borderColor: bdColors[2],
                    borderWidth: 1
                },{
                    label: '订单总返佣',
                    data: JSON.parse('{:json_encode(array_column($statics,"order_rebate"))}'),
                    backgroundColor: bgColors[3],
                    borderColor: bdColors[3],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero:true
                        }
                    }]
                }
            }
        });
    </script>
{/block}