{extend name="public:base" /}
{block name="header"}
    <style type="text/css">
        html{overflow-y:scroll;}
    </style>
{/block}
{block name="body"}

    {include file="public/bread" menu="test_rand_bouns" title="随机金额测试" /}

    <div id="page-wrapper">
        <div class="list-header">
            <form class="noajax" action="{:url('test/rand_bouns')}" method="post">
                <div class="form-row">
                    <div class="form-group col input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text">金额</span>
                        </div>
                        <input type="text" class="form-control" name="amount"  value="{$amount}">
                        <div class="input-group-middle"><span class="input-group-text">数量</span></div>
                        <input type="text" class="form-control" name="count" value="{$count}">
                        <div class="input-group-middle"><span class="input-group-text">小数位</span></div>
                        <input type="text" class="form-control" name="precision" value="{$precision}">
                    </div>
                    <div class="form-group col input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text">正态系数</span>
                        </div>
                        <input type="text" class="form-control fromdate" name="ratio"  value="{$ratio}">
                        <div class="input-group-middle"><span class="input-group-text">离散度</span></div>
                        <input type="text" class="form-control todate" name="disperse" value="{$disperse}">
                    </div>
                    <div class="form-group col">

                        <input type="submit" class="btn btn-primary btn-sm btn-submit ml-2" value="确定"/>
                        <a class="btn btn-info btn-sm" href="javascript:" onclick="location.reload();">刷新</a>
                    </div>
                </div>
            </form>
        </div>
        <div class="chart-box">
        <canvas id="myChart" width="800" height="400"></canvas>
        </div>
    </div>

{/block}
{block name="script"}
    <script type="text/javascript" src="__STATIC__/chart/Chart.bundle.min.js"></script>
    <script type="text/javascript">
        var ctx = document.getElementById("myChart");
        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: JSON.parse('{:json_encode($amounts)}'),
                datasets: [{
                    label: '金额分布',
                    data: JSON.parse('{:json_encode($amounts)}'),
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255,99,132,1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                bezierCurve : false,
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