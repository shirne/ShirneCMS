<extend name="public:base" />

<block name="body">

<include file="public/bread" menu="paylog_cashin" title="" />

<div id="page-wrapper">

    <div class="row list-header">
        <div class="col-6">
            <div class="btn-group btn-group-sm" role="group" aria-label="Button group with nested dropdown">
                <button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    导出订单
                </button>
                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                    <a class="dropdown-item" href="{:url('paylog/export',['order_ids'=>$orderids])}" target="_blank" >导出本页</a>
                    <a class="dropdown-item" href="{:url('paylog/export',['status'=>1])}" target="_blank">导出未处理</a>
                    <a class="dropdown-item" href="{:url('paylog/export',['status'=>$status,'key'=>base64_encode($key)])}" target="_blank">导出筛选结果</a>
                </div>
            </div>
            <span class="ml-3">总有效金额: {$total|showmoney}</span>
        </div>
        <div class="col-6">
            <form action="{:url('Paylog/cashin')}" method="post">
                <div class="form-row">
                    <div class="col form-group">
                        <select name="status" class="form-control form-control-sm">
                            <option value="">全部</option>
                            <option value="1"{$status==='1'?' selected':''}>已审核</option>
                            <option value="0"{$status==='0'?' selected':''}>未审核</option>
                        </select>
                    </div>
                    <div class="col form-group input-group input-group-sm">
                        <input type="text" class="form-control" value="{$keyword}" name="key" placeholder="输入名称搜索">
                        <div class="input-group-append">
                          <button class="btn btn-outline-secondary" type="submit"><i class="ion-md-search"></i></button>
                        </div>
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
            <th width="160">&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <volist name="lists" id="v" empty="$empty">
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
                <td>{$v.status|audit_status|raw}</td>
                <td class="operations">
                    <if condition="$v['status'] EQ 0">
                    <a class="btn btn-outline-success link-confirm" title="确认" href="{:url('Paylog/cashupdate',array('id'=>$v['id']))}"><i class="ion-md-check"></i> </a>
                    <a class="btn btn-outline-danger link-confirm" title="无效" data-confirm="您真的确定要作废吗？" href="{:url('Paylog/cashdelete',array('id'=>$v['id']))}" ><i class="ion-md-trash"></i> </a>
                        <else/>
                        -
                    </if>
                </td>
            </tr>
        </volist>
        </tbody>
    </table>
    {$page|raw}
</div>

</block>