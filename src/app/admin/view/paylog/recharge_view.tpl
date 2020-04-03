<table class="table table-hover table-striped">
    <tbody>
    <tr>
        <th width="80">申请ID</th>
        <td>{$model.id}&nbsp;{$v.status|audit_status|raw}</td>
    </tr>
    <tr>
        <th>会员</th>
        <td>[{$model.id}]{$model.username}<br />{$model.mobile}</td>
    </tr>
    <tr>
        <th>充值金额</th>
        <td>{$model.amount|showmoney}</td>
    </tr>
    <tr>
        <th>充值方式</th>
        <td>{$paytype[$v['paytype_id']]['title']}
            <if condition="$paytype['type'] EQ 'unioncard'">
                <br />{$v.cardname}<br />{$v.cardno}
            </if>
        </td>
    </tr>
    <tr>
        <th>日期</th>
        <td>{$model.create_time|showdate}</td>
    </tr>
    <tr>
        <th>凭证截图</th>
        <td><a href="{$model.pay_bill}" target="_blank"><img src="{$model.pay_bill}" class="img-fluid" /></a> </td>
    </tr>
    <tr>
        <th>备注</th>
        <td>{$model.remark}</td>
    </tr>
    </tbody>
</table>