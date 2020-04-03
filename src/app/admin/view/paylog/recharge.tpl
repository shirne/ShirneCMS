{extend name="public:base" /}

{block name="body"}

<include file="public/bread" menu="paylog_recharge" title=""/>

<div id="page-wrapper">

    <div class="row list-header">
        <div class="col-6">
            总有效金额: {$total|showmoney}
        </div>
        <div class="col-6">
            <form action="{:url('Paylog/recharge')}" method="post">
                <div class="form-group input-group input-group-sm">
                    <select class="form-control" name="status">
                        <option value="9">全部</option>
                        <option value="1" {$status==1?'selected':''}>待转帐</option>
                        <option value="2" {$status==2?'selected':''}>待处理</option>
                        <option value="3" {$status==3?'selected':''}>充值成功</option>
                        <option value="4" {$status==4?'selected':''}>无效单</option>
                    </select>
                    <span class="input-group-addon"></span>
                    <input type="text" class="form-control" value="{$keyword}" name="key" placeholder="输入名称搜索">
                    <div class="input-group-append">
                      <button class="btn btn-outline-secondary" type="submit"><i class="ion-md-search"></i></button>
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
            <th>付款方式</th>
            <th>金额</th>
            <th>支付说明</th>
            <th>下单时间</th>
            <th>状态</th>
            <th width="160">&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <php>$empty=list_empty(8);</php>
        {volist name="lists" id="v" empty="$empty"}
            <tr>
                <td>{$v.id}</td>
                <td>[{$v.member_id}]{$v.username}</td>
                <td>
                    {$paytype[$v['paytype_id']]['title']}
                    {if $v.type EQ 'unioncard'}
                        <br />{$v.cardname}[{$v.cardno}]
                    {/if}
                </td>
                <td>{$v.amount|showmoney}</td>
                <td>{$v.remark}</td>
                <td>{$v.create_time|showdate='Y-m-d H:i:s'}</td>
                <td>{$v.status|audit_status|raw}</td>
                <td class="operations">
                    <a href="{:url('paylog/rechargeView',['id'=>$v['id']])}" title="查看" class="btn btn-sm btn-outline-info" rel="ajax"><i class="ion-md-paper"></i> </a>
                    {if $v['status'] EQ 0}
                    <a class="btn btn-outline-success link-confirm" title="确认" data-confirm="确定该订单已到账？" href="{:url('Paylog/rechargeupdate',array('id'=>$v['id']))}"><i class="ion-md-checkmark-circle"></i> </a>
                    <a class="btn btn-outline-danger link-confirm" title="无效" data-confirm="您真的确定要作废吗？" href="{:url('Paylog/rechargedelete',array('id'=>$v['id']))}" ><i class="ion-md-trash"></i> </a>
                        {elseif $v['status'] EQ 1/}
                        <a class="btn btn-outline-primary link-confirm" title="撤销" href="{:url('Paylog/rechargecancel',array('id'=>$v['id']))}" ><i class="ion-md-history"></i> </a>
                        {else/}
                        -
                    {/if}
                </td>
            </tr>
        {/volist}
        </tbody>
    </table>
    {$page|raw}
</div>

{/block}