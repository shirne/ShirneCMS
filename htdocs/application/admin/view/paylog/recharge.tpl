<extend name="Public:Base" />

<block name="body">

<include file="Public/bread" menu="paylog_recharge" section="系统" title="充值记录" />

<div id="page-wrapper">

    <div class="row">
        <div class="col-6">
            总有效金额: {$total|showmoney}
        </div>
        <div class="col-6">
            <form action="{:url('Paylog/recharge')}" method="post">
                <div class="form-group input-group">
                    <select class="form-control" name="status">
                        <option value="9">全部</option>
                        <option value="1" {$status==1?'selected':''}>待转帐</option>
                        <option value="2" {$status==2?'selected':''}>待处理</option>
                        <option value="3" {$status==3?'selected':''}>充值成功</option>
                        <option value="4" {$status==4?'selected':''}>无效单</option>
                    </select>
                    <span class="input-group-addon"></span>
                    <input type="text" class="form-control" value="{$key}" name="key" placeholder="输入名称搜索">
                    <div class="input-group-append">
                      <button class="btn btn-outline-secondary" type="submit"><i class="fa fa-search"></i></button>
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
            <th width="150">操作</th>
        </tr>
        </thead>
        <tbody>
        <foreach name="lists" item="v">
            <tr>
                <td>{$v.id}</td>
                <td>[{$v.member_id}]{$v.username}</td>
                <td>
                    {$paytype[$v['paytype_id']]['title']}
                    <if condition="$v.type EQ 'unioncard'">
                        <br />{$v.cardname}[{$v.cardno}]
                    </if>
                </td>
                <td>{$v.amount|showmoney}</td>
                <td>{$v.remark}</td>
                <td>{$v.create_time|showdate='Y-m-d H:i:s'}</td>
                <td>{$v.status|o_status}</td>
                <td>
                    <if condition="$v['status'] EQ 0">
                    <a class="btn btn-default btn-sm" href="{:url('Paylog/rechargeupdate',array('id'=>$v['id']))}"><i class="fa fa-check"></i> 确认</a>
                    <a class="btn btn-default btn-sm" href="{:url('Paylog/rechargedelete',array('id'=>$v['id']))}" style="color:red;" onclick="javascript:return del('您真的确定要作废吗？');"><i class="fa fa-trash"></i> 无效</a>
                        <elseif condition="$v['status'] EQ 1"/>
                        <a class="btn btn-default btn-sm" href="{:url('Paylog/rechargecancel',array('id'=>$v['id']))}"  style="color:red;"><i class="fa fa-history"></i> 撤销</a>
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