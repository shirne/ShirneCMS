{extend name="public:base" /}

{block name="body"}

    {include file="public/bread" menu="member_authen_index" title="会员认证列表" /}

    <div id="page-wrapper">

        <div class="row list-header">
            <div class="col-6">
            </div>
            <div class="col-6">
            </div>
        </div>
        <table class="table table-hover table-striped">
            <thead>
            <tr>
                <th width="50">编号</th>
                <th>会员</th>
                <th>申请资料</th>
                <th>申请地区</th>
                <th>提交日期</th>
                <th>状态</th>
                <th width="160">&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            {empty name="lists"}{:list_empty(7)}{/empty}
            {foreach $lists as $key => $v}
                <tr>
                    <td>{$v.id}</td>
                    <td>
                        <div class="media">
                            {if !empty($v['avatar'])}
                                <img src="{$v.avatar}" class="mr-2 rounded" width="30"/>
                            {/if}
                            <div class="media-body">
                                <h5 class="mt-0 mb-1" style="font-size:13px;">
                                    {if !empty($v['nickname'])}
                                        {$v.nickname}
                                    {else/}
                                        {$v.username}
                                    {/if}
                                </h5>
                                <div style="font-size:12px;">
                                    [{$v.member_id} {$levels[$v['level_id']]['level_name']}]
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>{$v.realname}<br />{$v.mobile}</td>
                    <td>{$v.province}<br />{$v.city}</td>
                    <td>{$v.create_time|showdate}<br />{$v.create_time|showdate}</td>
                    <td>
                        {if $v['status'] == 1}
                            <span class="badge badge-success">已审核</span>
                        {elseif $v['status'] == -1/}
                            <span class="badge badge-warning">待审核</span>
                        {else/}
                            <span class="badge badge-secondary">已驳回</span>
                        {/if}
                    </td>
                    <td class="operations">
                        <a class="btn btn-outline-primary" title="审核" href="{:url('memberAuthen/update',array('id'=>$v['id']))}"><i class="ion-md-create"></i> </a>
                        <a class="btn btn-outline-danger link-confirm" title="删除" data-confirm="您真的确定要删除吗？\n删除后将不能恢复!" href="{:url('memberAuthen/delete',array('id'=>$v['id']))}" ><i class="ion-md-trash"></i> </a>
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
{/block}