{extend name="public:base" /}

{block name="body"}

    {include file="public/bread" menu="member_level_index" title="会员组列表" /}

    <div id="page-wrapper">

        <div class="row list-header">
            <div class="col-6">
                <a href="{:url('MemberLevel/agent')}" class="btn btn-outline-success btn-sm"><i class="ion-md-medal"></i> 代理等级</a>
                <a href="{:url('MemberLevel/add')}" class="btn btn-outline-primary btn-sm"><i class="ion-md-add"></i> 添加等级</a>
            </div>
            <div class="col-6">
            </div>
        </div>
        <table class="table table-hover table-striped">
            <thead>
            <tr>
                <th width="50">编号</th>
                <th>名称</th>
                <th>排序</th>
                <th>会员折扣</th>
                <th>消费额度</th>
                <th>消费佣金</th>
                <th width="160">&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            {foreach $lists as $key => $v}
                <tr>
                    <td>{$v.level_id}</td>
                    <td>{$v.level_name}[{$v.short_name}]{if $v['is_default']}<span class="badge badge-info">默认</span> {/if}</td>
                    <td>{$v.sort}</td>
                    <td>{$v['discount']>=100?'无':(round($v['discount']/100,1).'折')}</td>
                    <td>{$v.level_price}</td>
                    <td>{:implode('%, ',(array)@json_decode($v['commission_percent'],1))}{$v['commission_layer']?'%':''}</td>
                    <td class="operations">
                        <a class="btn btn-outline-primary" title="编辑" href="{:url('memberLevel/update',array('id'=>$v['level_id']))}"><i class="ion-md-create"></i> </a>
                        <a class="btn btn-outline-danger link-confirm" title="删除" data-confirm="您真的确定要删除吗？\n删除后将不能恢复!" href="{:url('memberLevel/delete',array('id'=>$v['level_id']))}" ><i class="ion-md-trash"></i> </a>
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
{/block}