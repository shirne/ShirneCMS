{extend name="public:base" /}

{block name="body"}

    {include  file="public/bread" menu="Board" section="主面板" title="账务统计" /}
    <div id="page-wrapper">
        <div class="card border-default">
            <div class="card-header">
                <h5 class="panel-title">概况</h5>
            </div>
            <table class="table table-striped text-right">
                <thead>
                <tr>
                    <th class="text-center" width="80">时段</th>
                    <th>今日</th>
                    <th>本月</th>
                    <th>总计</th>
                </tr>
                </thead>
                <tr>
                    <th class="text-center">消费</th>
                    <td>{$inout.day_in|showmoney}</td>
                    <td>{$inout.month_in|showmoney}</td>
                    <td>{$inout.in|showmoney}</td>
                </tr>
                <tr>
                    <th class="text-center">奖励</th>
                    <td>{$inout.day_out|showmoney}</td>
                    <td>{$inout.month_out|showmoney}</td>
                    <td>{$inout.out|showmoney}</td>
                </tr>
                <tr>
                    <th class="text-center">结余</th>
                    <td>{:showmoney($inout['day_in']-$inout['day_out'])}</td>
                    <td>{:showmoney($inout['month_in']-$inout['month_out'])}</td>
                    <td>{:showmoney($inout['in']-$inout['out'])}</td>
                </tr>
            </table>
        </div>

    </div>

{/block}