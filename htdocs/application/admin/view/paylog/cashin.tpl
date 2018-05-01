<extend name="Public:Base" />

<block name="body">

<include file="Public/bread" menu="paylog_cashin" section="会员" title="提现记录" />

<div id="page-wrapper">

    <div class="row">
        <div class="col-6">
            总有效金额: {$total|showmoney}
        </div>
        <div class="col-6">
            <form action="{:url('Paylog/cashin')}" method="post">
                <div class="form-group input-group">
                    <input type="text" class="form-control" value="{$key}" name="key" placeholder="输入名称搜索">
                    <div class="input-group-append">
                      <button class="btn btn-outline-secondary" type="submit"><i class="ion-search"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <table class="table table-hover table-striped">
        <thead>
        <tr>
            <th width="50">编号</th>
            <th>会员</th>
            <th>提现金额</th>
            <th>应转款</th>
            <th>提现方式</th>
            <th>提现信息</th>
            <th>下单时间</th>
            <th>状态</th>
            <th width="150">操作</th>
        </tr>
        </thead>
        <tbody>
        <foreach name="lists" item="v">
            <tr>
                <td>{$v.id}</td>
                <td>[{$v.member_id}]{$v.username}</td>
                <td>{$v.amount|showmoney}</td>
                <td>{$v.real_amount|showmoney}</td>
                <td>{$v.cashtype|showcashtype}</td>
                <td>
                    <if condition="$v.cashtype EQ 'unioncard'">
                    {$v.bank_name}<br />{$v.cardno|fmtCardno}
                        <else/>
                        {$v.cardno}
                    </if>
                </td>
                <td>{$v.create_time|showdate='Y-m-d H:i:s'}</td>
                <td>{$v.status|o_status}</td>
                <td>
                    <if condition="$v['status'] EQ 0">
                    <a class="btn btn-default btn-sm" href="{:url('Paylog/cashupdate',array('id'=>$v['id']))}"><i class="ion-check"></i> 确认</a>
                    <a class="btn btn-default btn-sm" href="{:url('Paylog/cashdelete',array('id'=>$v['id']))}" style="color:red;" onclick="javascript:return del('您真的确定要作废吗？');"><i class="ion-trash"></i> 无效</a>
                        <else/>
                        -
                    </if>
                </td>
            </tr>
        </foreach>
        </tbody>
    </table>
    {$page}
</div>

</block>