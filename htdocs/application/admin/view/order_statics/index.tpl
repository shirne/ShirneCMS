<extend name="public:base" />

<block name="body">

    <include file="public/bread" menu="order_statics_index" title="订单统计" />

    <div id="page-wrapper">
        <canvas id="myChart" width="800" height="400"></canvas>
    </div>

</block>
<block name="script">
    <script type="text/javascript" src="__STATIC__/chart/Chart.bundle.min.js"></script>
    <script type="text/javascript">
        var ctx = document.getElementById("myChart");
        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: JSON.parse('{:json_encode(array_column($statics,"awdate"))}'),
                datasets: [{
                    label: '会员下单量',
                    data: JSON.parse('{:json_encode(array_column($statics,"order_count"))}'),
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
</block>