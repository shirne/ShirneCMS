{extend name="public:base" /}
{block name="header"}
<style type="text/css">
    html {
        overflow-y: scroll;
    }
</style>
{/block}
{block name="body"}

{include file="public/bread" menu="member_index" title="会员统计" /}

<div id="page-wrapper">
    <div class="list-header">
        <form class="noajax" action="{:url('member.index/statics')}" method="post">
            <div class="form-row">
                <div class="form-group col input-group input-group-sm date-range">
                    <div class="input-group-prepend">
                        <span class="input-group-text">时间范围</span>
                    </div>
                    <input type="text" class="form-control fromdate" name="start_date" placeholder="选择开始日期"
                        value="{$start_date}">
                    <div class="input-group-middle"><span class="input-group-text">-</span></div>
                    <input type="text" class="form-control todate" name="end_date" placeholder="选择结束日期"
                        value="{$end_date}">
                </div>
                <div class="form-group col">
                    <div class="btn-group btn-group-sm btn-group-toggle" data-toggle="buttons">
                        <label class="btn btn-outline-secondary {$static_type=='date'?'active':''}">
                            <input type="radio" name="type" id="option1" value="date" autocomplete="off" checked> 按日
                        </label>
                        <label class="btn btn-outline-secondary {$static_type=='month'?'active':''}">
                            <input type="radio" name="type" id="option2" value="month" autocomplete="off"> 按月
                        </label>
                        <label class="btn btn-outline-secondary {$static_type=='year'?'active':''}">
                            <input type="radio" name="type" id="option3" value="year" autocomplete="off"> 按年
                        </label>
                    </div>
                    <input type="submit" class="btn btn-primary btn-sm btn-submit ml-2" value="确定" />
                </div>
            </div>
        </form>
    </div>
    <div class="row">
        <div class="chart-box col col-lg-8">
            <canvas id="myChart" width="800" height="400"></canvas>
            <div class="text-muted">注：代理数量统计依据为升级V1代理记录，如操作异常可能导致数据不准确</div>
        </div>
        <div class="table-box col col-lg-4">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th width="50">日期</th>
                        <th>注册数量</th>
                        <th>代理数量</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach $statics as $item}
                    <tr>
                        <th>{$item['awdate']}</th>
                        <td>{$item['member_count']}</td>
                        <td>{$item['agent_count']|default=0}</td>
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
    var bgColors = [
        'rgba(255, 99, 132, 0.2)',
        'rgba(54, 162, 235, 0.2)',
        'rgba(255, 206, 86, 0.2)',
        'rgba(75, 192, 192, 0.2)',
        'rgba(153, 102, 255, 0.2)',
        'rgba(255, 159, 64, 0.2)'
    ];
    var bdColors = [
        'rgba(255,99,132,1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 159, 64, 1)'
    ];
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: JSON.parse('{:json_encode(array_column($statics,"awdate"))}'),
            datasets: [{
                label: '会员注册量',
                data: JSON.parse('{:json_encode(array_column($statics,"member_count"))}'),
                backgroundColor: bgColors[0],
                borderColor: bdColors[0],
                borderWidth: 1
            }, {
                label: '升级代理数',
                data: JSON.parse('{:json_encode(array_column($statics,"agent_count"))}'),
                backgroundColor: bgColors[1],
                borderColor: bdColors[1],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });
</script>
{/block}