    <table class="table table-hover table-striped">
        <tbody>
            <tr>
                <th width="80">日志ID</th>
                <td>{$m.id}</td>
            </tr>
            <tr>
                <th>会员</th>
                <td>{$member.username}</td>
            </tr>
            <tr>
                <th>操作</th>
                <td>{$m.action}</td>
            </tr>
            <tr>
                <th>结果</th>
                <td>{if $m.result EQ 1}<span class="badge badge-success">成功</span>{else/}<span class="badge badge-danger">失败</span> {/if}</td>
            </tr>
            <tr>
                <th>日期</th>
                <td>{$m.create_time|showdate}</td>
            </tr>
            <tr>
                <th>IP</th>
                <td>{$m.ip}</td>
            </tr>
            <tr>
                <th>备注</th>
                <td>{$m.remark|print_remark}</td>
            </tr>
        </tbody>
    </table>